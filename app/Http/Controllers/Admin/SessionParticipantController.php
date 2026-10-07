<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionParticipant;
use App\Models\SportSession;
use App\Models\User;
use App\Services\SessionParticipantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionParticipantController extends Controller
{
    public function __construct(protected SessionParticipantService $participantService)
    {
    }

    public function index(SportSession $session)
    {
        $session->load(['sport', 'location']);

        $participants = SessionParticipant::with('user')
            ->where('session_id', $session->id)
            ->get();

        $participantUserIds = $participants->pluck('user_id')->toArray();

        $availableMembers = User::where('role', 'member')
            ->where('status', 'active')
            ->whereNotIn('id', $participantUserIds)
            ->orderBy('name')
            ->get();

        return view('admin.sessions.participants', compact('session', 'participants', 'availableMembers'));
    }

    public function store(Request $request, SportSession $session)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
            'registration_source' => ['nullable', 'in:whatsapp,manual'],
        ]);

        $addedCount = $this->participantService->addParticipants(
            session: $session,
            userIds: $validated['user_ids'],
            source: $validated['registration_source'] ?? 'manual',
            actor: Auth::user()
        );

        return back()->with('success', "Berhasil menambahkan {$addedCount} peserta ke sesi.");
    }

    public function destroy(SportSession $session, SessionParticipant $participant)
    {
        if ($participant->session_id !== $session->id) {
            abort(403);
        }

        $result = $this->participantService->removeParticipant($session, $participant, Auth::user());

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }

    public function import(Request $request, SportSession $session)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,txt'],
            'registration_source' => ['nullable', 'in:whatsapp,manual'],
        ]);

        $result = $this->participantService->importFromCsv(
            session: $session,
            file: $request->file('import_file'),
            source: $request->input('registration_source', 'whatsapp'),
            actor: Auth::user()
        );

        $message = "Berhasil mengimpor {$result['imported_count']} peserta.";
        if (count($result['failed_rows']) > 0) {
            return back()->with('success', $message)->with('import_errors', $result['failed_rows']);
        }

        return back()->with('success', $message);
    }
}
