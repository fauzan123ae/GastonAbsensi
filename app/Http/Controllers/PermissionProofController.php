<?php

namespace App\Http\Controllers;

use App\Models\PermissionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermissionProofController extends Controller
{
    /**
     * Download / Stream permission proof file safely.
     * Accessible only by Admin or the owner of the permission request.
     */
    public function show(PermissionRequest $permission)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Akses ditolak.');
        }

        if (!$user->isAdmin() && $permission->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk melihat berkas bukti izin ini.');
        }

        if (!$permission->proof) {
            abort(404, 'Berkas bukti izin tidak ditemukan.');
        }

        // Support both new 'local' disk and legacy 'public' disk
        $disk = 'local';
        if (!Storage::disk('local')->exists($permission->proof)) {
            if (Storage::disk('public')->exists($permission->proof)) {
                $disk = 'public';
            } else {
                abort(404, 'Berkas fisik tidak ditemukan pada server.');
            }
        }

        return Storage::disk($disk)->response($permission->proof);
    }
}
