<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // Listar grupos
    public function index()
    {
        $groups = Group::orderBy('name')->get();
        return view('admin.groups', compact('groups'));
    }

    // Crear grupo
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:groups,name',
        ]);

        Group::create(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Grupo creado correctamente.']);
    }

    // Actualizar grupo
    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:groups,name,' . $group->id,
        ]);

        $group->update(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Grupo actualizado correctamente.']);
    }

    // Eliminar gruupo
    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json(['success' => true, 'message' => 'Grupo eliminado correctamente.']);
    }
}
