<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Auth\TokenGuard;

class LegacyCompatibleTokenGuard extends TokenGuard
{
    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        $token = $this->getTokenForRequest();

        if (empty($token)) {
            return $this->user = null;
        }

        return $this->user = $this->retrieveByToken($token);
    }

    public function validate(#[\SensitiveParameter] array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        return (bool) $this->retrieveByToken((string) $credentials[$this->inputKey]);
    }

    private function retrieveByToken(string $token)
    {
        return $this->provider->retrieveByCredentials([
            $this->storageKey => hash('sha256', $token),
        ]) ?? $this->provider->retrieveByCredentials([
            $this->storageKey => $token,
        ]);
    }
}
