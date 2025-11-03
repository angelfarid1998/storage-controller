<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;

class GroupController extends Controller
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

    // ver grupos
    public function index()
    {
        $groups = Group::orderBy('name')->get();
        return view('admin.groups.index', compact('groups'));
    }

    // nuevo grupo
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:groups,name',
                'quota' => 'required|numeric|min:1024'
            ]);

            Group::create($request->only(['name', 'quota']));

            return response()->json([
                'success' => true,
                'message' => 'Grupo creado correctamente.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $error = $e->validator->errors()->first();
            return response()->json(['success' => false, 'message' => $error], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // actualizar cuota y nombre de grupo
    public function update(Request $request, Group $group)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:groups,name,' . $group->id,
                'quota' => 'required|numeric|min:1024'
            ]);

            $group->update($request->only(['name', 'quota']));

            return response()->json([
                'success' => true,
                'message' => 'Grupo actualizado correctamente.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $error = $e->validator->errors()->first();
            return response()->json(['success' => false, 'message' => $error], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // eliminargrupo
    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json([
            'success' => true,
            'message' => 'Grupo eliminado correctamente.'
        ]);
    }
}
