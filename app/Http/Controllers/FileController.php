<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Services\FileValidationService;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar los archivos del usuario autenticado
     */
    public function index()
    {
        $files = auth()->user()->files()->latest()->get();
        return view('user.files', compact('files'));
    }

    // Guardar un nuevo archivo
    public function store(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240', // 10 MB
            ]);

            $file = $request->file('file');

            // 🔍 Validar tipo, tamaño y cuota disponible
            $validator = new FileValidationService();
            $validator->validateFile($file, auth()->user());

            // 📁 Carpeta destino: storage/app/public/uploads/{user_id}
            $uploadPath = 'uploads/' . auth()->id();

            // 🧹 Limpieza del nombre original
            $originalName = $file->getClientOriginalName();
            $cleanName = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $originalName);

            // 🚫 Evitar colisiones (si ya existe, agrega timestamp)
            $path = $uploadPath . '/' . $cleanName;
            if (Storage::disk('public')->exists($path)) {
                $filenameWithoutExt = pathinfo($cleanName, PATHINFO_FILENAME);
                $extension = pathinfo($cleanName, PATHINFO_EXTENSION);
                $cleanName = $filenameWithoutExt . '_' . time() . '.' . $extension;
                $path = $uploadPath . '/' . $cleanName;
            }

            // 💾 Guardar archivo en storage/public/uploads/{user_id}/
            Storage::disk('public')->putFileAs($uploadPath, $file, $cleanName);

            // 🗂️ Registrar en base de datos
            File::create([
                'user_id' => auth()->id(),
                'filename' => $cleanName,
                'size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Archivo cargado correctamente.'
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Error al subir archivo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor.',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 500);
        }
    }

    //  Descargar archivo
    public function download($id)
    {
        $file = File::findOrFail($id);

        if ($file->user_id !== auth()->id() && auth()->user()->role->name !== 'admin') {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        return Storage::disk('public')->download('uploads/' . $file->user_id . '/' . $file->filename);
    }
    // Eliminar archivo
    public function destroy($id)
    {
        $file = File::findOrFail($id);

        if ($file->user_id !== auth()->id() && auth()->user()->role->name !== 'admin') {
            abort(403, 'No tienes permiso para eliminar este archivo.');
        }

        Storage::disk('public')->delete('uploads/' . $file->user_id . '/' . $file->filename);
        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'Archivo eliminado correctamente.'
        ]);
    }
}
