<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Service;
use App\Models\SlaPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::with(['category', 'slaPolicy'])->orderBy('sort_order');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        $services   = $query->paginate(20)->withQueryString();
        $categories = Category::active()->topLevel()->get();
        $slaPolicies= SlaPolicy::where('is_active', true)->get();

        return view('admin.services.index', compact('services', 'categories', 'slaPolicies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'category_id'   => ['nullable', 'exists:categories,id'],
            'sla_policy_id' => ['nullable', 'exists:sla_policies,id'],
            'icon'          => ['nullable', 'string', 'max:60'],
            'is_active'     => ['boolean'],
            'sort_order'    => ['nullable', 'integer'],
        ]);

        $validated['slug']      = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $service = Service::create($validated);

        AuditLog::record('service.created', 'service',
            "Layanan '{$service->name}' dibuat oleh " . auth()->user()->name,
            auth()->id(),
            Service::class, $service->id,
            null, $validated,
        );

        return back()->with('success', "Layanan '{$service->name}' berhasil dibuat.");
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'category_id'   => ['nullable', 'exists:categories,id'],
            'sla_policy_id' => ['nullable', 'exists:sla_policies,id'],
            'icon'          => ['nullable', 'string', 'max:60'],
            'is_active'     => ['boolean'],
            'sort_order'    => ['nullable', 'integer'],
        ]);

        $old = $service->toArray();
        $validated['is_active'] = $validated['is_active'] ?? false;

        $service->update($validated);

        AuditLog::record('service.updated', 'service',
            "Layanan '{$service->name}' diperbarui oleh " . auth()->user()->name,
            auth()->id(),
            Service::class, $service->id,
            $old, $validated,
        );

        return back()->with('success', "Layanan '{$service->name}' berhasil diperbarui.");
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->requests()->exists()) {
            return back()->with('error', "Tidak dapat menghapus layanan yang memiliki permintaan.");
        }

        $name = $service->name;
        $service->delete();

        AuditLog::record('service.deleted', 'service',
            "Layanan '{$name}' dihapus oleh " . auth()->user()->name,
            auth()->id(),
        );

        return back()->with('success', "Layanan '{$name}' telah dihapus.");
    }
}
