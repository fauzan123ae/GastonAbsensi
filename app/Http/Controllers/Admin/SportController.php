<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::withCount('sessions')->orderBy('name')->get();
        return view('admin.sports.index', compact('sports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $sport = Sport::create($validated);

        AuditLogService::log(
            action: 'create_sport',
            targetType: 'Sport',
            targetId: $sport->id,
            description: "Admin membuat cabang olahraga: {$sport->name}",
            newValues: $validated
        );

        return redirect()->route('admin.sports.index')->with('success', "Olahraga {$sport->name} berhasil ditambahkan.");
    }

    public function update(Request $request, Sport $sport)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $oldValues = $sport->only(['name', 'description', 'status']);
        $sport->update($validated);

        AuditLogService::log(
            action: 'update_sport',
            targetType: 'Sport',
            targetId: $sport->id,
            description: "Admin memperbarui cabang olahraga: {$sport->name}",
            oldValues: $oldValues,
            newValues: $validated
        );

        return redirect()->route('admin.sports.index')->with('success', "Olahraga {$sport->name} berhasil diperbarui.");
    }

    public function destroy(Sport $sport)
    {
        if ($sport->sessions()->exists()) {
            return back()->withErrors(['error' => 'Olahraga tidak dapat dihapus karena sudah memiliki riwayat sesi.']);
        }

        $name = $sport->name;
        $sport->delete();

        AuditLogService::log(
            action: 'delete_sport',
            targetType: 'Sport',
            description: "Admin menghapus cabang olahraga: {$name}"
        );

        return redirect()->route('admin.sports.index')->with('success', "Olahraga {$name} berhasil dihapus.");
    }
}
