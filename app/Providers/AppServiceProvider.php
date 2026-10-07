<?php

namespace App\Providers;

use App\Models\PermissionRequest;
use App\Models\SportSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.admin', 'layouts.member'], function ($view) {
            $user = Auth::user();
            $notifications = collect();

            if ($user) {
                if ($user->isAdmin()) {
                    // 1. Pending permissions needing review
                    $pendingPerms = PermissionRequest::with(['user', 'session.sport'])
                        ->where('status', 'pending')
                        ->latest()
                        ->take(4)
                        ->get();

                    foreach ($pendingPerms as $perm) {
                        $notifications->push([
                            'id' => 'perm-' . $perm->id,
                            'title' => 'Pengajuan Izin: ' . ($perm->user->name ?? 'Member'),
                            'message' => 'Izin pada sesi ' . ($perm->session->sport->name ?? 'Olahraga') . ' menunggu persetujuan.',
                            'time' => $perm->created_at->diffForHumans(),
                            'type' => 'warning',
                            'link' => route('admin.permissions.index', ['status' => 'pending']),
                        ]);
                    }

                    // 2. Today sessions
                    $todaySessions = SportSession::with('sport')
                        ->whereDate('date', now()->toDateString())
                        ->orderBy('start_time')
                        ->take(3)
                        ->get();

                    foreach ($todaySessions as $ses) {
                        $notifications->push([
                            'id' => 'session-' . $ses->id,
                            'title' => 'Sesi Hari Ini: ' . ($ses->sport->name ?? 'Olahraga'),
                            'message' => 'Jadwal dimulai pukul ' . substr($ses->start_time, 0, 5) . ' WIB.',
                            'time' => 'Hari ini',
                            'type' => 'info',
                            'link' => route('admin.sessions.scan', $ses),
                        ]);
                    }
                } else {
                    // Member notifications
                    // 1. Reviewed permissions for this member
                    $memberPerms = PermissionRequest::with('session.sport')
                        ->where('user_id', $user->id)
                        ->latest()
                        ->take(3)
                        ->get();

                    foreach ($memberPerms as $perm) {
                        $statusText = $perm->status === 'approved' ? 'disetujui' : ($perm->status === 'rejected' ? 'ditolak' : 'sedang diproses');
                        $notifications->push([
                            'id' => 'm-perm-' . $perm->id,
                            'title' => 'Status Izin ' . ucfirst($perm->status),
                            'message' => 'Izin sesi ' . ($perm->session->sport->name ?? 'Olahraga') . ' telah ' . $statusText . '.',
                            'time' => $perm->updated_at->diffForHumans(),
                            'type' => $perm->status === 'approved' ? 'success' : ($perm->status === 'rejected' ? 'danger' : 'warning'),
                            'link' => route('member.permissions.index'),
                        ]);
                    }

                    // 2. Upcoming active sessions registered
                    $upcoming = SportSession::with('sport')
                        ->whereHas('participants', function ($q) use ($user) {
                            $q->where('user_id', $user->id)->where('status', 'registered');
                        })
                        ->whereDate('date', '>=', now()->toDateString())
                        ->where('status', '!=', 'cancelled')
                        ->orderBy('date')
                        ->take(2)
                        ->get();

                    foreach ($upcoming as $ses) {
                        $notifications->push([
                            'id' => 'm-ses-' . $ses->id,
                            'title' => 'Agenda Olahraga: ' . ($ses->sport->name ?? 'Olahraga'),
                            'message' => 'Jadwal pada ' . $ses->date->translatedFormat('d M Y') . ' jam ' . substr($ses->start_time, 0, 5) . ' WIB.',
                            'time' => $ses->date->diffForHumans(),
                            'type' => 'info',
                            'link' => route('member.sessions.index'),
                        ]);
                    }
                }
            }

            $view->with('navbarNotifications', $notifications);
            $view->with('unreadNotificationsCount', $notifications->count());
        });
    }
}
