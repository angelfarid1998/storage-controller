<?php

namespace App\Services;

use App\Models\File;

class StorageQuotaService
{
    /**
     *
     * @param  \App\Models\User  $user
     * @param  int  $newFileSize Tamaño del archivo (bytes)
     * @return bool
     */
    public function hasSpace($user, int $newFileSize): bool
    {
        // Uso actual del usuario
        $used = File::where('user_id', $user->id)->sum('size');

        $quota = $this->resolveQuota($user);

        // Si no hay cuota definida usar 10 MB global
        if (!$quota) {
            $quota = 10 * 1024 * 1024;
        }

        $available = $quota - $used;

        if ($newFileSize > $available) {
            \Log::warning("⚠️ Usuario {$user->id} excedió la cuota: {$used}/{$quota} bytes usados.");
            return false;
        }

        return true;
    }

    public function resolveQuota($user): int
    {
        // Cuota individual - prioridad
        if (!empty($user->quota)) {
            return (int) $user->quota;
        }

        // Cuota del grupo
        if ($user->group && !empty($user->group->quota)) {
            return (int) $user->group->quota;
        }

        // Cuota global por defecto (10 MB)
        return 10 * 1024 * 1024;
    }
}
