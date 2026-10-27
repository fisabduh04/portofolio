<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class PegawaiUserProvider extends EloquentUserProvider
{
    /**
     * @param  array<string, mixed>  $credentials
     */
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        if (! array_key_exists('email', $credentials)) {
            return parent::retrieveByCredentials($credentials);
        }

        if (! is_string($credentials['email']) || trim($credentials['email']) === '') {
            return null;
        }

        $accounts = $this->newModelQuery()->with('pegawai')
            ->forLoginEmail($credentials['email'])->limit(2)->get();

        if ($accounts->count() !== 1) {
            return null;
        }

        $account = $accounts->first();
        $credentials['email'] = fn (Builder $query): Builder => $query->whereKey($account->getKey());

        return parent::retrieveByCredentials($credentials);
    }
}
