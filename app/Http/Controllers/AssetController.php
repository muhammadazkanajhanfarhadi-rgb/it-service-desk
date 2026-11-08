<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetMaintenance;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssetController extends Controller
{
    /* ── Index (all authenticated users can view) ──────── */

    public function index(Request $request): View
    {
        $query = Asset::with(['category', 'currentAssignment.user'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('asset_number', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
            );
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $assets     = $query->paginate(20)->withQueryString();
        $categories = Category::active()->get();

        // Dashboard stats
        $stats = [
            'total'       => Asset::count(),
            'available'   => Asset::available()->count(),
            'assigned'    => Asset::assigned()->count(),
            'maintenance' => Asset::maintenance()->count(),
            'retired'     => Asset::retired()->count(),
        ];

        return view('assets.index', compact('assets', 'categories', 'stats'));
    }

    /* ── Show detail ─────────────────────────────────────── */

    public function show(Asset $asset): View
    {
        $asset->load(['category', 'assignments.user', 'assignments.assigner', 'maintenanceRecords.performer']);

        return view('assets.show', compact('asset'));
    }

    /* ── Admin: Create ──────────────────────────────────── */

    public function create(): View
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        $categories = Category::active()->get();

        return view('assets.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'brand'          => ['nullable', 'string', 'max:100'],
            'model'          => ['nullable', 'string', 'max:100'],
            'serial_number'  => ['required', 'string', 'max:100', 'unique:assets'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'specifications'  => ['nullable', 'string'],
            'purchase_date'  => ['nullable', 'date'],
            'warranty_end'   => ['nullable', 'date', 'after_or_equal:purchase_date'],
        ]);

        $validated['asset_number'] = Asset::generateAssetNumber();
        $validated['status']       = Asset::AVAILABLE;
        $validated['is_active']     = true;

        $asset = Asset::create($validated);

        AuditLog::record('asset.created', 'asset',
            "Aset {$asset->asset_number} ({$asset->name}) dibuat oleh " . auth()->user()->name,
            auth()->id(), Asset::class, $asset->id, null, $validated,
        );

        return redirect()->route('assets.show', $asset)
            ->with('success', "Aset {$asset->asset_number} berhasil dibuat.");
    }

    /* ── Admin: Edit ─────────────────────────────────────── */

    public function edit(Asset $asset): View
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        $categories = Category::active()->get();

        return view('assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'brand'          => ['nullable', 'string', 'max:100'],
            'model'          => ['nullable', 'string', 'max:100'],
            'serial_number'  => ['required', 'string', 'max:100', 'unique:assets,serial_number,' . $asset->id],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'specifications'  => ['nullable', 'string'],
            'purchase_date'  => ['nullable', 'date'],
            'warranty_end'   => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'status'         => ['nullable', 'in:available,assigned,maintenance,retired'],
            'is_active'      => ['boolean'],
        ]);

        $old = $asset->toArray();
        $validated['is_active'] = $validated['is_active'] ?? false;

        $asset->update($validated);

        AuditLog::record('asset.updated', 'asset',
            "Aset {$asset->asset_number} diperbarui oleh " . auth()->user()->name,
            auth()->id(), Asset::class, $asset->id, $old, $validated,
        );

        return redirect()->route('assets.show', $asset)
            ->with('success', "Aset {$asset->asset_number} berhasil diperbarui.");
    }

    /* ── Admin: Delete ───────────────────────────────────── */

    public function destroy(Asset $asset): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        if ($asset->hasActiveAssignment()) {
            return back()->with('error', 'Tidak dapat menghapus aset yang masih memiliki assignment aktif.');
        }

        $name = $asset->asset_number;
        $asset->delete();

        AuditLog::record('asset.deleted', 'asset',
            "Aset {$name} dihapus oleh " . auth()->user()->name,
            auth()->id(),
        );

        return redirect()->route('assets.index')
            ->with('success', "Aset {$name} telah dihapus.");
    }

    /* ── Admin: Assign asset to user ──────────────────────── */

    public function assign(Request $request, Asset $asset): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'notes'   => ['nullable', 'string'],
        ]);

        // Prevent double assignment
        if ($asset->hasActiveAssignment()) {
            return back()->with('error', "Aset {$asset->asset_number} sedang di-assign. Kembalikan dulu sebelum assign ke user lain.");
        }

        // Prevent assigning non-available assets (except maintenance)
        if (!in_array($asset->status, [Asset::AVAILABLE, Asset::MAINTENANCE])) {
            return back()->with('error', "Aset dengan status {$asset->status} tidak dapat di-assign.");
        }

        DB::transaction(function () use ($asset, $validated, $request) {
            AssetAssignment::create([
                'asset_id'    => $asset->id,
                'user_id'     => $validated['user_id'],         // from validated input
                'assigned_by' => $request->user()->id,         // from auth, NOT input
                'assigned_at' => now(),
                'notes'       => $validated['notes'] ?? null,
            ]);

            $asset->update(['status' => Asset::ASSIGNED]);

            AuditLog::record('asset.assigned', 'asset',
                "Aset {$asset->asset_number} di-assign kepada " . User::find($validated['user_id'])->name . " oleh " . $request->user()->name,
                $request->user()->id, Asset::class, $asset->id,
            );
        });

        return back()->with('success', "Aset {$asset->asset_number} berhasil di-assign.");
    }

    /* ── Admin: Return asset ──────────────────────────────── */

    public function returnAsset(Request $request, Asset $asset): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $assignment = $asset->currentAssignment;

        if (!$assignment) {
            return back()->with('error', "Aset {$asset->asset_number} tidak sedang di-assign.");
        }

        DB::transaction(function () use ($assignment, $asset, $validated, $request) {
            $assignment->update([
                'returned_at' => now(),
                'notes'       => $validated['notes'] ?? $assignment->notes,
            ]);

            $asset->update(['status' => Asset::AVAILABLE]);

            AuditLog::record('asset.returned', 'asset',
                "Aset {$asset->asset_number} dikembalikan oleh " . $request->user()->name,
                $request->user()->id, Asset::class, $asset->id,
            );
        });

        return back()->with('success', "Aset {$asset->asset_number} berhasil dikembalikan.");
    }

    /* ── Admin: Record maintenance ───────────────────────── */

    public function storeMaintenance(Request $request, Asset $asset): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        $validated = $request->validate([
            'type'             => ['required', 'in:preventive,corrective,upgrade,inspection'],
            'description'      => ['nullable', 'string'],
            'technician'       => ['nullable', 'string', 'max:100'],
            'maintenance_date' => ['required', 'date'],
            'cost'             => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['performed_by'] = $request->user()->id;  // from auth, NOT input

        DB::transaction(function () use ($asset, $validated, $request) {
            AssetMaintenance::create(array_merge($validated, ['asset_id' => $asset->id]));

            // Set asset to maintenance status if not retired
            if (!$asset->isRetired()) {
                $asset->update(['status' => Asset::MAINTENANCE]);
            }

            AuditLog::record('asset.maintenance', 'asset',
                "Maintenance ({$validated['type']}) dicatat untuk aset {$asset->asset_number} oleh " . $request->user()->name,
                $request->user()->id, Asset::class, $asset->id,
            );
        });

        return back()->with('success', "Maintenance untuk {$asset->asset_number} berhasil dicatat.");
    }
}
