<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'admin@local.test')->first();
echo 'Role: '.$user->role."\n";

$request = Illuminate\Http\Request::create('/absensi/rekap-harian', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Since the auth middleware uses the auth guard, let's just log the user in
auth()->login($user);

$response = app()->handle($request);
echo 'Status: '.$response->status()."\n";
if ($response->status() != 200) {
    echo "Content: \n".substr($response->getContent(), 0, 1000)."\n";
} else {
    echo "Success! Content starts with: \n".substr($response->getContent(), 0, 200)."\n";
}
