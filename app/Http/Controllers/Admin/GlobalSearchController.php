<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportSession;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (empty($query)) {
            return response()->json([
                'members' => [],
                'sessions' => [],
                'menu' => $this->getDefaultMenus(),
            ]);
        }

        // 1. Filter Menu / Fitur
        $menus = collect($this->getAllMenus())->filter(function ($item) use ($query) {
            return str_contains(strtolower($item['title']), strtolower($query))
                || str_contains(strtolower($item['description']), strtolower($query))
                || str_contains(strtolower($item['keywords'] ?? ''), strtolower($query));
        })->values();

        // 2. Filter Members
        $members = User::where('role', 'member')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('member_id', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->take(5)
            ->get()
            ->map(function ($m) {
                return [
                    'title' => $m->name,
                    'subtitle' => $m->member_id . ' • ' . $m->email,
                    'url' => route('admin.members.show', $m),
                    'badge' => $m->isActive() ? 'Aktif' : 'Nonaktif',
                    'badge_type' => $m->isActive() ? 'success' : 'danger',
                ];
            });

        // 3. Filter Sesi Olahraga
        $sessions = SportSession::with(['sport', 'location'])
            ->where(function ($q) use ($query) {
                $q->whereHas('sport', function ($s) use ($query) {
                    $s->where('name', 'like', "%{$query}%");
                })->orWhereHas('location', function ($l) use ($query) {
                    $l->where('name', 'like', "%{$query}%");
                })->orWhere('status', 'like', "%{$query}%");
            })
            ->latest('date')
            ->take(5)
            ->get()
            ->map(function ($ses) {
                return [
                    'title' => ($ses->sport->name ?? 'Olahraga') . ' - ' . ($ses->location->name ?? 'GOR'),
                    'subtitle' => $ses->date->format('d M Y') . ' (' . substr($ses->start_time, 0, 5) . ' - ' . substr($ses->end_time, 0, 5) . ' WIB)',
                    'url' => route('admin.sessions.attendance', $ses),
                    'badge' => strtoupper($ses->status),
                    'badge_type' => $ses->status === 'open' ? 'info' : ($ses->status === 'ongoing' ? 'warning' : 'default'),
                ];
            });

        return response()->json([
            'menu' => $menus,
            'members' => $members,
            'sessions' => $sessions,
        ]);
    }

    private function getDefaultMenus()
    {
        return array_slice($this->getAllMenus(), 0, 5);
    }

    private function getAllMenus()
    {
        return [
            [
                'title' => 'Dashboard Utama',
                'description' => 'Ringkasan metrik kehadiran, kalender, dan quick action',
                'keywords' => 'beranda home statistik resume',
                'url' => route('admin.dashboard'),
                'icon' => 'home',
            ],
            [
                'title' => 'Sesi Olahraga',
                'description' => 'Kelola jadwal sesi, peserta terdaftar, dan scanner absensi',
                'keywords' => 'jadwal sesi scan scanner lapangan gor cabor',
                'url' => route('admin.sessions.index'),
                'icon' => 'calendar',
            ],
            [
                'title' => 'Buat Sesi Baru',
                'description' => 'Jadwalkan agenda olahraga baru untuk member',
                'keywords' => 'tambah sesi buat baru jadwal',
                'url' => route('admin.sessions.create'),
                'icon' => 'plus',
            ],
            [
                'title' => 'Daftar Member',
                'description' => 'Kelola akun member, kartu QR member permanen',
                'keywords' => 'anggota peserta user member profil data',
                'url' => route('admin.members.index'),
                'icon' => 'users',
            ],
            [
                'title' => 'Tambah Member',
                'description' => 'Daftarkan peserta member Gaston baru',
                'keywords' => 'registrasi register tambah anggota',
                'url' => route('admin.members.create'),
                'icon' => 'user-plus',
            ],
            [
                'title' => 'Laporan & Rekap Kehadiran',
                'description' => 'Statistik attendance rate, filter tanggal, dan unduh CSV',
                'keywords' => 'rekapitulasi export unduh csv riwayat history report',
                'url' => route('admin.reports.index'),
                'icon' => 'report',
            ],
            [
                'title' => 'Review Pengajuan Izin',
                'description' => 'Tinjau permohonan izin berhalangan dari peserta',
                'keywords' => 'perizinan izin sakit cuti dispensasi surat dokter pending',
                'url' => route('admin.permissions.index'),
                'icon' => 'document',
            ],
            [
                'title' => 'Master Cabang Olahraga',
                'description' => 'Kelola data cabor futsal, badminton, basket, dll.',
                'keywords' => 'cabor olahraga sport badminton futsal',
                'url' => route('admin.sports.index'),
                'icon' => 'tag',
            ],
            [
                'title' => 'Master Lokasi / GOR',
                'description' => 'Kelola daftar gedung olahraga dan lapangan',
                'keywords' => 'gor lokasi tempat venue arena',
                'url' => route('admin.locations.index'),
                'icon' => 'map-pin',
            ],
            [
                'title' => 'Audit Log / Log Aktivitas',
                'description' => 'Riwayat audit trail seluruh aksi admin dan pengguna',
                'keywords' => 'log audit riwayat sistem keamanan riwayat aktivitas pengaturan',
                'url' => route('admin.audit-logs.index'),
                'icon' => 'shield',
            ],
            [
                'title' => 'Kelola Admin & Master',
                'description' => 'Tambah akun pengelola baru, atur hak akses peran, suspend akun',
                'keywords' => 'admin master kelola role superadmin hak akses',
                'url' => route('admin.admins.index'),
                'icon' => 'key',
            ],
        ];
    }
}
