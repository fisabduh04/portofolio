<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view the list of models.
     */
    public function viewAny(User $actor): bool
    {
        return (bool) $actor->is_active && $actor->isManagement();
    }

    /**
     * Determine if the authenticated user can manage (update role, toggle active) the target user.
     */
    public function manage(User $actor, User $target): bool
    {
        // 1. Self-immunity: Cannot manage self
        if (! $this->viewAny($actor) || $actor->id === $target->id) {
            return false;
        }

        // 2. ATURAN KHUSUS KEPALA:
        // Hanya Kepala yang boleh menyentuh akun Kepala lain (termasuk mempromosikan orang jadi Kepala)
        if ($target->isKepala() && ! $actor->isKepala()) {
            return false;
        }

        // 3. General Rule: Actor must have Higher OR Equal rank than Target
        return $actor->role->canAssign($target->role);
    }

    // Alias for updating role
    public function updateRole(User $actor, User $target): bool
    {
        return $this->manage($actor, $target);
    }

    // Alias for toggling status
    public function toggleStatus(User $actor, User $target): bool
    {
        return $this->manage($actor, $target);
    }
}
