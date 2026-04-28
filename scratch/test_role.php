<?php

use App\Models\User;
use App\Enums\UserRole;

echo "--- START TESTING ROLE SYSTEM ---\n";

$users = User::limit(5)->get();

if ($users->isEmpty()) {
    echo "Gagal: Tidak ada user di database untuk dites.\n";
    return;
}

foreach ($users as $user) {
    echo "User: " . $user->name . "\n";
    echo "  - Role String: " . $user->getAttributes()['role'] . "\n";
    
    // Cek apakah casting berhasil
    if ($user->role instanceof UserRole) {
        echo "  - Casting Berhasil: YA (Enum Instance)\n";
        echo "  - Label: " . $user->role->label() . "\n";
        echo "  - Rank: " . $user->role->rank() . "\n";
        echo "  - canManagePayroll: " . ($user->canManagePayroll() ? "BOLEH" : "DILARANG") . "\n";
    } else {
        echo "  - Casting Gagal: BUKAN ENUM (Masih string: " . gettype($user->role) . ")\n";
    }
    echo "---------------------------------\n";
}

echo "--- END TESTING ---\n";
