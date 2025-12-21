<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function index(): View
    {
        return view('admin.modules.index', [
            'modules' => Module::orderBy('order', 'asc')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.modules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'key' => 'required|string|unique:modules,key',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'route' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        Module::create([
            'name' => $validated['name'],
            'key' => $validated['key'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'route' => $validated['route'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.modules.index')->with('success', 'Module created');
    }

    public function edit(Module $module): View
    {
        return view('admin.modules.edit', [
            'module' => $module,
        ]);
    }

    public function update(Request $request, Module $module): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'key' => 'required|string|unique:modules,key,' . $module->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'route' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $module->update([
            'name' => $validated['name'],
            'key' => $validated['key'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'route' => $validated['route'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.modules.index')->with('success', 'Module updated');
    }

    public function destroy(Module $module): RedirectResponse
    {
        $module->delete();
        return redirect()->route('admin.modules.index')->with('success', 'Module deleted');
    }
}
