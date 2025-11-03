<?php

namespace App\Services;

use App\Models\Setting;
use App\Services\StorageQuotaService;
use ZipArchive;

class FileValidationService
{
    protected $forbiddenExtensions = [];

    public function __construct()
    {
        // Cargar extensiones prohibidas
        $setting = Setting::where('key', 'forbidden_extensions')->first();
        $this->forbiddenExtensions = $setting
            ? array_map('trim', explode(',', $setting->value))
            : ['exe', 'bat', 'js', 'php', 'sh'];
    }

    /**
     * Validar la extención.
     */
    public function validateFile($file, $user)
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, $this->forbiddenExtensions)) {
            abort(422, "Error: El tipo de archivo .$ext no está permitido.");
        }

        // Validar espacio disponible
        $quotaService = new StorageQuotaService();
        if (!$quotaService->hasSpace($user, $file->getSize())) {
            $cuotaMB = number_format($quotaService->resolveQuota($user) / 1024 / 1024, 2);
            abort(422, "Error: Cuota de almacenamiento ({$cuotaMB} MB) excedida.");
        }

        // Si es un ZIP inspeccionar su contenido
        if ($ext === 'zip') {
            $this->inspectZip($file);
        }
    }

    /**
     * Verifica que dentro de un ZIP no haya archivos con extensiones prohibidas.
     */
    private function inspectZip($file)
    {
        $zip = new ZipArchive();
        $path = $file->getRealPath();

        if ($zip->open($path) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($ext, $this->forbiddenExtensions)) {
                    $zip->close();
                    abort(422, "Error: El archivo '$name' dentro del ZIP no está permitido.");
                }
            }
            $zip->close();
        }
    }
}
