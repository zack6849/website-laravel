<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('api_token')
            ->orderBy('id')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $apiToken = (string) $user->api_token;
                    $hashedToken = $this->hashApiToken($apiToken);

                    if (hash_equals($apiToken, $hashedToken)) {
                        continue;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['api_token' => $hashedToken]);
                }
            });
    }

    public function down(): void
    {
        // API token hashing is intentionally one-way.
    }

    private function hashApiToken(string $apiToken): string
    {
        return $this->isHashedApiToken($apiToken) ? strtolower($apiToken) : hash('sha256', $apiToken);
    }

    private function isHashedApiToken(string $apiToken): bool
    {
        return preg_match('/\A[0-9a-f]{64}\z/i', $apiToken) === 1;
    }
};
