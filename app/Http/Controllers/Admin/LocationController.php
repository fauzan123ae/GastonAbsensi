<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('sessions')->orderBy('name')->get();
        return view('admin.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $location = Location::create($validated);

        AuditLogService::log(
            action: 'create_location',
            targetType: 'Location',
            targetId: $location->id,
            description: "Admin membuat lokasi/GOR: {$location->name}",
            newValues: $validated
        );

        return redirect()->route('admin.locations.index')->with('success', "Lokasi/GOR {$location->name} berhasil ditambahkan.");
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $oldValues = $location->only(['name', 'address', 'description', 'status']);
        $location->update($validated);

        AuditLogService::log(
            action: 'update_location',
            targetType: 'Location',
            targetId: $location->id,
            description: "Admin memperbarui lokasi/GOR: {$location->name}",
            oldValues: $oldValues,
            newValues: $validated
        );

        return redirect()->route('admin.locations.index')->with('success', "Lokasi/GOR {$location->name} berhasil diperbarui.");
    }

    public function destroy(Location $location)
    {
        if ($location->sessions()->exists()) {
            return back()->withErrors(['error' => 'Lokasi tidak dapat dihapus karena sudah memiliki riwayat sesi.']);
        }

        $name = $location->name;
        $location->delete();

        AuditLogService::log(
            action: 'delete_location',
            targetType: 'Location',
            description: "Admin menghapus lokasi/GOR: {$name}"
        );

        return redirect()->route('admin.locations.index')->with('success', "Lokasi/GOR {$name} berhasil dihapus.");
    }
}
