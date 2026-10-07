<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportSession;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrScannerController extends Controller
{
    public function __construct(protected AttendanceService $attendanceService)
    {
    }

    public function show(SportSession $session)
    {
        $session->load(['sport', 'location']);

        $summary = $this->attendanceService->getSessionSummary($session);

        return view('admin.sessions.scan', compact('session', 'summary'));
    }

    /**
     * AJAX endpoint to lookup/verify scanned QR token or Member ID
     */
    public function scan(Request $request, SportSession $session)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $result = $this->attendanceService->verifyMemberForSession($session, $request->qr_token);

        $memberData = null;
        if ($result['member']) {
            $m = $result['member'];
            $memberData = [
                'id' => $m->id,
                'member_id' => $m->member_id,
                'name' => $m->name,
                'email' => $m->email,
                'phone' => $m->phone,
                'photo_url' => $m->photo ? asset('storage/' . $m->photo) : null,
                'status' => $m->status,
            ];
        }

        $attendanceData = null;
        if ($result['attendance']) {
            $attendanceData = [
                'checked_in_at' => $result['attendance']->checked_in_at->format('H:i'),
                'method' => $result['attendance']->method,
            ];
        }

        return response()->json([
            'success' => $result['success'],
            'code' => $result['code'],
            'message' => $result['message'],
            'is_registered' => $result['is_registered'],
            'member' => $memberData,
            'attendance' => $attendanceData,
        ]);
    }

    /**
     * AJAX endpoint to confirm attendance after Admin checks identity/photo
     */
    public function confirm(Request $request, SportSession $session)
    {
        $request->validate([
            'member_id' => ['required', 'exists:users,id'],
            'is_walk_in' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $member = User::findOrFail($request->member_id);
        if (!$member->isMember()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun admin tidak dapat dicatat sebagai peserta kehadiran.',
            ], 422);
        }

        $isWalkIn = $request->boolean('is_walk_in');

        try {
            if ($isWalkIn) {
                $attendance = $this->attendanceService->recordWalkIn(
                    session: $session,
                    member: $member,
                    admin: Auth::user(),
                    method: 'qr',
                    notes: $request->notes ?? 'Walk-in QR'
                );
            } else {
                $attendance = $this->attendanceService->confirmAttendance(
                    session: $session,
                    member: $member,
                    admin: Auth::user(),
                    method: 'qr',
                    notes: $request->notes
                );
            }

            $summary = $this->attendanceService->getSessionSummary($session);

            return response()->json([
                'success' => true,
                'message' => "Kehadiran {$member->name} ({$member->member_id}) berhasil diverifikasi!",
                'attendance' => [
                    'id' => $attendance->id,
                    'checked_in_at' => $attendance->checked_in_at->format('H:i'),
                ],
                'summary' => $summary,
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Member sudah hadir.',
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Attendance confirmation error: ' . $e->getMessage(), [
                'exception' => $e,
                'session_id' => $session->id,
                'member_id' => $member->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan sistem saat memproses kehadiran.',
            ], 422);
        }
    }
}
