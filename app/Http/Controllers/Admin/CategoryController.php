<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::with('parent')
            ->withCount('children')
            ->orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->paginate(20)->withQueryString();
        $parents    = Category::topLevel()->active()->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'icon'       => ['nullable', 'string', 'max:60'],
            'color'      => ['nullable', 'string', 'max:20'],
            'parent_id'  => ['nullable', 'exists:categories,id'],
            'description'=> ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $validated['slug']      = Str::slug(($validated['parent_id'] ? Category::find($validated['parent_id'])->name . '-' : '') . $validated['name']);
        $validated['is_active'] = true;

        Category::create($validated);

        return back()->with('success', "Kategori '{$validated['name']}' berhasil dibuat.");
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'icon'       => ['nullable', 'string', 'max:60'],
            'color'      => ['nullable', 'string', 'max:20'],
            'parent_id'  => ['nullable', 'exists:categories,id'],
            'description'=> ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['boolean'],
        ]);

        if (isset($validated['name']) && $validated['name'] !== $category->name) {
            $parent = isset($validated['parent_id']) ? Category::find($validated['parent_id']) : null;
            $validated['slug'] = Str::slug(($parent?->name ? $parent->name . '-' : '') . $validated['name']);
        }
        $validated['is_active'] = $validated['is_active'] ?? false;

        $category->update($validated);

        return back()->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()->with('error', "Tidak dapat menghapus kategori yang memiliki subkategori.");
        }

        $name = $category->name;
        $category->delete();

        return back()->with('success', "Kategori '{$name}' telah dihapus.");
    }
}
