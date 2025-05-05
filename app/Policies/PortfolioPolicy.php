<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;

class PortfolioPolicy
{
    /**
     * Determina si el usuario puede crear imágenes en el portafolio.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Solo los admins pueden crear
    }

    /**
     * Determina si el usuario puede eliminar imágenes del portafolio.
     */
    public function delete(User $user, Portfolio $portfolio): bool
    {
        return $user->isAdmin(); // Solo los admins pueden eliminar
    }

    /**
     * Determina si el usuario puede actualizar una imagen del portafolio.
     */
    public function update(User $user, Portfolio $portfolio): bool
    {
        return $user->isAdmin(); // Solo los administradores pueden editar
    }
}
