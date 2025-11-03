<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\User;
use App\Models\Group;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Si es admin muestra las estadísticas
        if ($user->role->name === 'admin') {
            $stats = [
                'usuarios' => \App\Models\User::count(),
                'grupos' => \App\Models\Group::count(),
                'archivos' => \App\Models\File::count(),
            ];

            // Datos para gráfico
            $groupData = \App\Models\Group::with('users.files')->get()->map(function ($group) {
                $total = 0;
                foreach ($group->users as $u) {
                    $total += $u->files->sum('size');
                }
                return [
                    'group' => $group->name,
                    'storage' => round($total / (1024 * 1024), 2), // MB
                    'users' => $group->users->count(),
                ];
            });

            return view('dashboard.admin', compact('user', 'stats', 'groupData'));
        }

        // Si es usuario ve estadísticas personales
        $used = File::where('user_id', $user->id)->sum('size');
        $quota = $user->quota ?? $user->group->quota ?? 10485760;
        $available = max(0, $quota - $used);

        $stats = [
            'used' => $used,
            'quota' => $quota,
            'available' => $available,
            'files' => File::where('user_id', $user->id)->latest()->take(5)->get(),
        ];

        return view('dashboard.user', compact('user', 'stats'));
    }
}
