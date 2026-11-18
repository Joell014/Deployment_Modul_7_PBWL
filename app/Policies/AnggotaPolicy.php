<?php

namespace App\Policies;

use App\Models\Anggota;
use App\Models\User;

class AnggotaPolicy
{
    public function view(User $user, Anggota $anggota): bool
    {
        return $user->hasAnyRole(['admin', 'petugas'])
            || $anggota->user_id === $user->id;
    }
}