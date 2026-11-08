<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::with(['category', 'slaPolicy'])
            ->active()
            ->orderBy('sort_order');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $services = $query->paginate(12)->withQueryString();
        $categories = \App\Models\Category::active()->topLevel()->get();

        return view('service-catalog.index', compact('services', 'categories'));
    }

    public function show(Service $service): View
    {
        $service->load(['category', 'slaPolicy']);

        return view('service-catalog.show', compact('service'));
    }
}
