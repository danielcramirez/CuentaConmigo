<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('admin.banners.index', [
            'banners' => Banner::orderBy('order', 'asc')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image_url' => 'required|string',
            'target_url' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        Banner::create([
            'image_url' => $validated['image_url'],
            'target_url' => $validated['target_url'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', [
            'banner' => $banner,
        ]);
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'image_url' => 'required|string',
            'target_url' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $banner->update([
            'image_url' => $validated['image_url'],
            'target_url' => $validated['target_url'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted');
    }
}
