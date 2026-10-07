<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SportSession;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct(protected AttendanceService $attendanceService)
    {
    }

    public function index(Request $request, SportSession $session)
    {
        $session->load(['sport', 'location']);

        $statusFilter = $request->input('status', 'all');
        $reconciled = $this->attendanceService->getReconciledAttendanceList($session, $statusFilter);
        $summary = $this->attendanceService->getSessionSummary($session);

        return view('admin.sessions.attendance', compact('session', 'reconciled', 'statusFilter', 'summary'));
    }

    public function manualStore(Request $request, SportSession $session)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:255'],
            'allow_walk_in' => ['nullable', 'boolean'],
        ]);

        $result = $this->attendanceService->verifyMemberForSession($session, $request->identifier);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        $member = $result['member'];

        try {
            if ($result['is_registered']) {
                $this->attendanceService->confirmAttendance(
                    session: $session,
                    member: $member,
                    admin: Auth::user(),
                    method: 'manual',
                    notes: $request->notes ?? 'Manual Attendance by Admin'
                );
                return back()->with('success', "Kehadiran manual {$member->name} ({$member->member_id}) berhasil dicatat!");
            } else {
                if (!$request->boolean('allow_walk_in')) {
                    return back()->withInput()->withErrors([
                        'error' => "Member {$member->name} ({$member->member_id}) belum terdaftar pada sesi ini. Centang opsi 'Catat sebagai Walk-in' untuk mengonfirmasi kehadiran.",
                    ]);
                }

                $this->attendanceService->recordWalkIn(
                    session: $session,
                    member: $member,
                    admin: Auth::user(),
                    method: 'manual',
                    notes: $request->notes ?? 'Walk-in Manual'
                );
                return back()->with('success', "Walk-in manual {$member->name} ({$member->member_id}) berhasil dicatat!");
            }
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->withErrors(['error' => 'Member sudah hadir pada sesi ini.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancel(Request $request, SportSession $session, Attendance $attendance)
    {
        if ($attendance->session_id !== $session->id) {
            abort(403);
        }

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ]);

        $this->attendanceService->cancelAttendance($attendance, $request->cancellation_reason, Auth::user());

        return back()->with('success', "Kehadiran member {$attendance->user->name} berhasil dibatalkan.");
    }

    public function closeSession(SportSession $session)
    {
        $this->attendanceService->closeSession($session, Auth::user());

        return redirect()->route('admin.sessions.attendance', $session)->with('success', 'Sesi olahraga telah resmi diselesaikan. Rekonsiliasi kehadiran final disimpan.');
    }
}
