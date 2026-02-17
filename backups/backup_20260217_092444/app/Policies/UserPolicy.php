<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if the user can view the list of models.
     */
    public function viewAny(User $actor): bool
    {
        return in_array($actor->role, ['admin', 'operator', 'kepala']);
    }

    /**
     * Determine if the authenticated user can manage (update role, toggle active) the target user.
     */
    public function manage(User $actor, User $target): bool
    {
        // 1. Self-immunity: Cannot manage self (deactivate/change role)
        if ($actor->id === $target->id) {
            return false;
        }

        // 2. Define Hierarchy Ranks
        $ranks = [
            'kepala'   => 4,
            'admin'    => 3,
            'operator' => 2,
            'guru'     => 1,
            'siswa'    => 1,
        ];

        $actorRank  = $ranks[$actor->role] ?? 0;
        $targetRank = $ranks[$target->role] ?? 0;

        // 3. Special Case: Kepala can manage other Kepala (if multiple exist)
        if ($actor->role === 'kepala' && $target->role === 'kepala') {
            return true;
        }

        // 4. General Rule: Actor must have Higher OR Equal rank than Target
        return $actorRank >= $targetRank;
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
