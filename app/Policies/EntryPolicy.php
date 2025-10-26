<?php

namespace App\Policies;

use App\Models\Entry;
use App\Models\User;

class EntryPolicy {
    public function view(User $u, Entry $e): bool {
        return $u->isAdmin()
            || $u->isTeacher() && $u->groups->contains('id', $e->group_id)
            || $u->isStudent() && $u->groups->contains('id', $e->group_id);
    }

    public function create(User $u): bool {
        return $u->isAdmin() || $u->isTeacher() || $u->isStudent();
    }

    public function update(User $u, Entry $e): bool {
        if ($u->isAdmin()) return true;
        if ($u->isTeacher()) return $u->groups->contains('id', $e->group_id);
        if ($u->isStudent()) return $e->created_by === $u->id;
        return false;
    }

    public function delete(User $u, Entry $e): bool {
        if ($u->isAdmin()) return true;
        if ($u->isTeacher()) return $u->groups->contains('id', $e->group_id);
        if ($u->isStudent()) return $e->created_by === $u->id; // interpreted as “Ausblenden”
        return false;
    }
}
