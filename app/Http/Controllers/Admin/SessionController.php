<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Sport;
use App\Models\SportSession;
use App\Services\AttendanceService;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function __construct(protected SessionService $sessionService)
    {
    }

    public function index(Request $request)
    {
        $sportId = $request->input('sport_id');
        $locationId = $request->input('location_id');
        $status = $request->input('status');
        $date = $request->input('date');

        $sessions = $this->sessionService->getPaginatedSessions($request->all());

        $sports = Sport::where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();

        return view('admin.sessions.index', compact('sessions', 'sports', 'locations', 'sportId', 'locationId', 'status', 'date'));
    }

    public function create()
    {
        $sports = Sport::where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();

        return view('admin.sessions.create', compact('sports', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sport_id' => ['required', 'exists:sports,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'status' => ['required', 'in:draft,open,ongoing,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $session = $this->sessionService->createSession($validated, Auth::user());

        return redirect()->route('admin.sessions.index')->with('success', 'Sesi olahraga berhasil dibuat!');
    }

    public function show(SportSession $session, AttendanceService $attendanceService)
    {
        $session->load(['sport', 'location', 'creator']);
        $summary = $attendanceService->getSessionSummary($session);

        $participants = $session->participants()
            ->with(['user', 'user.attendances' => function ($q) use ($session) {
                $q->where('session_id', $session->id);
            }])
            ->get();

        return view('admin.sessions.show', compact('session', 'summary', 'participants'));
    }

    public function edit(SportSession $session)
    {
        $sports = Sport::where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();

        return view('admin.sessions.edit', compact('session', 'sports', 'locations'));
    }

    public function update(Request $request, SportSession $session)
    {
        $validated = $request->validate([
            'sport_id' => ['required', 'exists:sports,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'status' => ['required', 'in:draft,open,ongoing,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->sessionService->updateSession($session, $validated, Auth::user());

        return redirect()->route('admin.sessions.show', $session)->with('success', 'Data sesi berhasil diperbarui.');
    }

    public function updateStatus(Request $request, SportSession $session)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,open,ongoing,completed,cancelled'],
        ]);

        $status = $this->sessionService->updateStatus($session, $validated['status'], Auth::user());

        return back()->with('success', "Status sesi berhasil diubah menjadi: {$status}");
    }
}
