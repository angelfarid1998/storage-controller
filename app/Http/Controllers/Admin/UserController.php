<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role->name !== 'admin') {
                abort(403, 'Acceso denegado');
            }
            return $next($request);
        });
    }

    // Lista usuarios
    public function index()
    {
        $users = \App\Models\User::with(['group', 'role', 'files'])->get();
        $groups = \App\Models\Group::orderBy('name')->get();

        // Calcular uso total de cada usuario
        foreach ($users as $user) {
            $user->used_space = $user->files->sum('size');

            // Determinar la cuota
            if ($user->quota) {
                $user->quota_limit = $user->quota;
            } elseif ($user->group && $user->group->quota) {
                $user->quota_limit = $user->group->quota;
            } else {
                $user->quota_limit = 10 * 1024 * 1024; // 10 MB default
            }
        }

        return view('admin.users.index', compact('users', 'groups'));
    }

    // asignar grupo
    public function assignGroup(Request $request, User $user)
    {
        $request->validate([
            'group_id' => 'nullable|exists:groups,id',
            'quota' => 'nullable|numeric|min:1024'
        ]);

        $user->update([
            'group_id' => $request->group_id,
            'quota' => $request->quota
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Datos del usuario actualizados correctamente.'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'group_id' => 'nullable|exists:groups,id',
            'quota' => 'nullable|numeric|min:1024'
        ]);

        $user->update([
            'group_id' => $request->group_id,
            'quota' => $request->quota
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Datos del usuario actualizados correctamente.'
        ]);
    }
}
