<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\User;
use App\Services\SlaService;
use App\Services\NotificationService;
use App\Services\TicketHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        private SlaService            $slaService,
        private TicketHistoryService  $historyService,
        private NotificationService   $notifService,
    ) {}

    /* ── Index: All Tickets (Admin / Manager / Technician) ── */

    public function index(Request $request): View
    {
        $user  = $request->user();
        $query = Ticket::with(['requester', 'assignee', 'priority', 'category'])
                       ->orderByDesc('created_at');

        // Technician: only assigned tickets
        if ($user->isTechnician()) {
            $query->assignedTo($user);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->whereHas('priority', fn($q) => $q->where('slug', $request->priority));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('ticket_number', 'like', "%{$search}%")
            );
        }

        $tickets = $query->paginate(20)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    /* ── My Tickets (Employee view) ──────────────────────── */

    public function myTickets(Request $request): View
    {
        $user = $request->user();

        $query = Ticket::with(['priority', 'category'])
                       ->forUser($user)
                       ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(15)->withQueryString();

        return view('tickets.my-tickets', compact('tickets'));
    }

    /* ── Create ──────────────────────────────────────────── */

    public function create(): View
    {
        $categories = Category::active()->topLevel()->with('children')->get();
        $priorities  = Priority::active()->get();
        $locations   = Location::where('is_active', true)->get();

        return view('tickets.create', compact('categories', 'priorities', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority_id' => ['nullable', 'exists:priorities,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'impact'      => ['required', 'in:low,medium,high,critical'],
            'urgency'     => ['required', 'in:low,medium,high,critical'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip'],
        ]);

        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'status'        => Ticket::STATUS_NEW,
            'requester_id'  => $request->user()->id,
            'category_id'   => $validated['category_id'] ?? null,
            'priority_id'   => $validated['priority_id'] ?? null,
            'location_id'   => $validated['location_id'] ?? null,
            'impact'        => $validated['impact'],
            'urgency'       => $validated['urgency'],
        ]);

        // Apply SLA
        $this->slaService->applyToTicket($ticket);
        $ticket->save();

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedName = $file->hashName();
                $path       = $file->storeAs('tickets/' . $ticket->id, $storedName, 'private');

                $ticket->attachments()->create([
                    'uploaded_by'  => $request->user()->id,
                    'original_name'=> $file->getClientOriginalName(),
                    'stored_name'  => $storedName,
                    'file_path'    => $path,
                    'mime_type'    => $file->getMimeType(),
                    'file_size'    => $file->getSize(),
                ]);
            }
        }

        // Record history
        $this->historyService->recordCreated($ticket, $request->user());

        // Notify admins/managers
        $this->notifService->notifyTicketCreated($ticket);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Tiket {$ticket->ticket_number} berhasil dibuat.");
    }

    /* ── Show / Detail ───────────────────────────────────── */

    public function show(Request $request, Ticket $ticket): View
    {
        $user = $request->user();

        // Employees can only see their own tickets
        if ($user->isEmployee() && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        $ticket->load([
            'requester', 'assignee', 'priority', 'category',
            'slaPolicy', 'location',
            'comments.user',
            'attachments.uploader',
            'histories.user',
        ]);

        // Update SLA breach status
        if ($this->slaService->checkBreaches($ticket)) {
            $ticket->save();
        }

        $technicians = User::whereHas('role', fn($q) => $q->where('slug', 'technician'))
                           ->where('is_active', true)
                           ->get();

        return view('tickets.show', compact('ticket', 'technicians'));
    }

    /* ── Update Status ───────────────────────────────────── */

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'status'           => ['required', 'string'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $newStatus = $validated['status'];

        if (!$ticket->canTransitionTo($newStatus)) {
            return back()->with('error', "Transisi status tidak diizinkan.");
        }

        $oldStatus  = $ticket->status;
        $ticket->status = $newStatus;

        if ($newStatus === Ticket::STATUS_RESOLVED) {
            $ticket->resolved_at       = now();
            $ticket->resolution_notes  = $validated['resolution_notes'] ?? null;
        }
        if ($newStatus === Ticket::STATUS_CLOSED) {
            $ticket->closed_at = now();
        }

        $ticket->save();

        $this->historyService->recordStatusChange($ticket, $user, $oldStatus, $newStatus);
        $this->notifService->notifyStatusChanged($ticket, $oldStatus, $newStatus);

        return back()->with('success', "Status tiket diperbarui ke <strong>{$newStatus}</strong>.");
    }

    /* ── Assign Technician ───────────────────────────────── */

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        if (!$user->canManage() && !$user->isTechnician()) {
            abort(403);
        }

        $validated = $request->validate([
            'assignee_id' => ['required', 'exists:users,id'],
        ]);

        $assignee = User::findOrFail($validated['assignee_id']);

        $ticket->update([
            'assignee_id' => $assignee->id,
            'status'      => $ticket->status === Ticket::STATUS_NEW
                             ? Ticket::STATUS_ASSIGNED
                             : $ticket->status,
        ]);

        $this->historyService->recordAssigned($ticket, $user, $assignee);
        $this->notifService->notifyAssigned($ticket, $assignee);

        // Mark first response if technician is responding
        $this->slaService->recordFirstResponse($ticket);
        $ticket->save();

        return back()->with('success', "Tiket di-assign kepada {$assignee->name}.");
    }

    /* ── Add Comment ─────────────────────────────────────── */

    public function addComment(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        // Employee can only see/add public comments on their own ticket
        if ($user->isEmployee() && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1'],
            'type' => ['required', 'in:public,internal'],
        ]);

        // Employees can only post public comments
        if ($user->isEmployee()) {
            $validated['type'] = 'public';
        }

        $ticket->comments()->create([
            'user_id' => $user->id,
            'body'    => $validated['body'],
            'type'    => $validated['type'],
        ]);

        // Record first response from technician
        if ($user->isTechnician() || $user->canManage()) {
            $this->slaService->recordFirstResponse($ticket);
            $ticket->save();
        }

        $this->historyService->record(
            $ticket, $user,
            TicketHistory::ACTION_COMMENT_ADDED,
            "Komentar ditambahkan oleh {$user->name}",
        );

        $this->notifService->notifyCommentAdded($ticket, $user, $validated['type']);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
