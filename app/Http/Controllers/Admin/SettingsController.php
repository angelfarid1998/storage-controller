<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
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

    public function index()
    {
        $defaultQuota = Setting::where('key', 'default_quota')->first()?->value ?? 10 * 1024 * 1024;
        $forbiddenExt = Setting::where('key', 'forbidden_extensions')->first()?->value ?? 'exe,bat,js,php,sh';

        return view('admin.settings.index', compact('defaultQuota', 'forbiddenExt'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'default_quota' => 'required|numeric|min:1024',
            'forbidden_extensions' => 'required|string'
        ]);

        Setting::updateOrCreate(
            ['key' => 'default_quota'],
            ['value' => $request->default_quota]
        );

        Setting::updateOrCreate(
            ['key' => 'forbidden_extensions'],
            ['value' => $request->forbidden_extensions]
        );

        return response()->json([
            'success' => true,
            'message' => 'Configuraciones guardadas correctamente.'
        ]);
    }
}
