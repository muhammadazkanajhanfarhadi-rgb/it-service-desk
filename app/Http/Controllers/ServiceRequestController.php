<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServiceRequestController extends Controller
{
    public function __construct(
        private NotificationService $notifService,
    ) {}

    /* ── Employee: My Requests ────────────────────────── */

    public function myRequests(Request $request): View
    {
        $query = ServiceRequest::with(['service', 'approver'])
            ->forUser($request->user())
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15)->withQueryString();

        return view('service-requests.my-requests', compact('requests'));
    }

    /* ── Employee: Create Request ──────────────────────── */

    public function create(Service $service): View
    {
        $service->load(['category', 'slaPolicy']);
        return view('service-requests.create', compact('service'));
    }

    public function store(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'description'   => ['required', 'string', 'min:10'],
            'request_data'  => ['nullable', 'array'],
        ]);

        $sr = ServiceRequest::create([
            'request_number' => ServiceRequest::generateRequestNumber(),
            'service_id'     => $service->id,
            'user_id'        => $request->user()->id,  // from auth, not input
            'status'         => ServiceRequest::PENDING_APPROVAL,
            'description'    => $validated['description'],
            'request_data'   => $validated['request_data'] ?? null,
        ]);

        AuditLog::record('service_request.created', 'service_request',
            "Permintaan {$sr->request_number} untuk layanan '{$service->name}' dibuat oleh " . $request->user()->name,
            $request->user()->id,
            ServiceRequest::class, $sr->id,
        );

        $this->notifService->notifyServiceRequestCreated($sr);

        return redirect()->route('service-requests.my')
            ->with('success', "Permintaan {$sr->request_number} berhasil diajukan.");
    }

    /* ── Employee: Cancel own request ──────────────────── */

    public function cancel(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        if ($serviceRequest->user_id !== $request->user()->id) {
            abort(403);
        }

        if (!in_array($serviceRequest->status, [ServiceRequest::PENDING_APPROVAL])) {
            return back()->with('error', 'Hanya permintaan dengan status pending yang dapat dibatalkan.');
        }

        $serviceRequest->update(['status' => ServiceRequest::CANCELLED]);

        AuditLog::record('service_request.cancelled', 'service_request',
            "Permintaan {$serviceRequest->request_number} dibatalkan oleh " . $request->user()->name,
            $request->user()->id,
            ServiceRequest::class, $serviceRequest->id,
        );

        return back()->with('success', 'Permintaan telah dibatalkan.');
    }

    /* ── Approver/Manager: Pending Approvals ──────────── */

    public function pendingApprovals(Request $request): View
    {
        $user = $request->user();

        if (!$user->canManage() && !$user->isApprover()) {
            abort(403);
        }

        $query = ServiceRequest::with(['service', 'user'])
            ->pendingApproval()
            ->orderByDesc('created_at');

        $requests = $query->paginate(15)->withQueryString();

        return view('service-requests.approvals', compact('requests'));
    }

    /* ── Approver/Manager: Approve / Reject ────────────── */

    public function approve(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $user = $request->user();
        if (!$user->canManage() && !$user->isApprover()) {
            abort(403);
        }

        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($serviceRequest, $user, $validated) {
            $serviceRequest->update([
                'status'         => ServiceRequest::APPROVED,
                'approved_by'    => $user->id,      // from auth, not input
                'approved_at'    => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            AuditLog::record('service_request.approved', 'service_request',
                "Permintaan {$serviceRequest->request_number} disetujui oleh {$user->name}",
                $user->id,
                ServiceRequest::class, $serviceRequest->id,
                ['status' => ServiceRequest::PENDING_APPROVAL],
                ['status' => ServiceRequest::APPROVED],
            );
        });

        $this->notifService->notifyServiceRequestDecision($serviceRequest, $user, true);

        return back()->with('success', "Permintaan {$serviceRequest->request_number} disetujui.");
    }

    public function reject(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $user = $request->user();
        if (!$user->canManage() && !$user->isApprover()) {
            abort(403);
        }

        $validated = $request->validate([
            'approval_notes' => ['required', 'string', 'min:5'],
        ]);

        DB::transaction(function () use ($serviceRequest, $user, $validated) {
            $serviceRequest->update([
                'status'         => ServiceRequest::REJECTED,
                'approved_by'    => $user->id,
                'rejected_at'     => now(),
                'approval_notes' => $validated['approval_notes'],
            ]);

            AuditLog::record('service_request.rejected', 'service_request',
                "Permintaan {$serviceRequest->request_number} ditolak oleh {$user->name}",
                $user->id,
                ServiceRequest::class, $serviceRequest->id,
                ['status' => ServiceRequest::PENDING_APPROVAL],
                ['status' => ServiceRequest::REJECTED],
            );
        });

        $this->notifService->notifyServiceRequestDecision($serviceRequest, $user, false);

        return back()->with('success', "Permintaan {$serviceRequest->request_number} ditolak.");
    }

    /* ── Approver/Manager: Mark In Progress / Completed ── */

    public function updateStatus(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $user = $request->user();
        if (!$user->canManage() && !$user->isApprover()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:in_progress,completed'],
        ]);

        $oldStatus = $serviceRequest->status;
        $updates = ['status' => $validated['status']];

        if ($validated['status'] === ServiceRequest::COMPLETED) {
            $updates['completed_at'] = now();
        }

        $serviceRequest->update($updates);

        AuditLog::record('service_request.status_changed', 'service_request',
            "Status permintaan {$serviceRequest->request_number} berubah dari {$oldStatus} menjadi {$validated['status']}",
            $user->id,
            ServiceRequest::class, $serviceRequest->id,
        );

        return back()->with('success', "Status diperbarui ke {$validated['status']}.");
    }
}
