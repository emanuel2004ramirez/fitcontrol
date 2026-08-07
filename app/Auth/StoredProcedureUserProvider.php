<?php

namespace App\Auth;

use App\Models\User;
use App\Services\UsuarioService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class StoredProcedureUserProvider implements UserProvider
{
    public function __construct(private readonly Hasher $hasher, private readonly UsuarioService $users) {}

    public function retrieveById($identifier): ?Authenticatable
    {
        return $this->hydrate($this->users->obtenerParaAutenticacion((int) $identifier));
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token): void {}

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        $login = $credentials['login'] ?? $credentials['username'] ?? $credentials['email'] ?? null;

        return is_string($login) ? $this->hydrate($this->users->obtenerCredenciales($login)) : null;
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials): bool
    {
        return $this->hasher->check((string) ($credentials['password'] ?? ''), $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false): void
    {
        if ($force || $this->hasher->needsRehash($user->getAuthPassword())) {
            $this->users->cambiarPassword(
                (int) $user->getAuthIdentifier(),
                $this->hasher->make($credentials['password']),
                (bool) $user->debe_cambiar_password,
            );
        }
    }

    private function hydrate(?object $record): ?User
    {
        if ($record === null) {
            return null;
        }
        $user = new User;
        $user->setRawAttributes((array) $record, true);
        $user->exists = true;

        return $user;
    }
}
