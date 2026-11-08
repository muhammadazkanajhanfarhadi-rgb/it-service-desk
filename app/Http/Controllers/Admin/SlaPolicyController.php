<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlaPolicyController extends Controller
{
    public function index(): View
    {
        $policies = SlaPolicy::with('priority')
            ->orderBy('priority_id')
            ->get();

        return view('admin.sla-policies.index', compact('policies'));
    }

    public function update(Request $request, SlaPolicy $slaPolicy): RedirectResponse
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'response_minutes'   => ['required', 'integer', 'min:1'],
            'resolution_minutes' => ['required', 'integer', 'min:1'],
            'business_hours_only'=> ['boolean'],
            'business_start'     => ['nullable', 'date_format:H:i'],
            'business_end'       => ['nullable', 'date_format:H:i'],
            'is_active'          => ['boolean'],
            'description'        => ['nullable', 'string'],
        ]);

        $validated['business_hours_only'] = $validated['business_hours_only'] ?? false;
        $validated['is_active']           = $validated['is_active'] ?? false;

        $slaPolicy->update($validated);

        return back()->with('success', "SLA Policy '{$slaPolicy->name}' berhasil diperbarui.");
    }
}
