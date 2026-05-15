<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'username')) {
            return;
        }

        DB::table('users')
            ->select(['id', 'name', 'email', 'username'])
            ->where(function ($query): void {
                $query->whereNull('username')->orWhere('username', '');
            })
            ->orderBy('id')
            ->chunkById(200, function ($users): void {
                foreach ($users as $user) {
                    $base = $this->buildBaseUsername($user->email, $user->name, (int) $user->id);
                    $candidate = $base;
                    $suffix = 1;

                    while (
                        DB::table('users')
                        ->where('username', $candidate)
                        ->where('id', '!=', $user->id)
                        ->exists()
                    ) {
                        $candidate = $base . '_' . $suffix;
                        $suffix++;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'username' => $candidate,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep generated usernames to avoid breaking logins.
    }

    private function buildBaseUsername(?string $email, ?string $name, int $id): string
    {
        $emailPrefix = is_string($email) ? explode('@', $email)[0] : '';
        $nameValue = is_string($name) ? $name : '';

        $base = trim((string) $emailPrefix) !== '' ? $emailPrefix : $nameValue;
        $base = strtolower($base);
        $base = preg_replace('/\s+/', '.', $base);
        $base = preg_replace('/[^a-z0-9._-]/', '', (string) $base);
        $base = trim((string) $base, '._-');

        if ($base === '') {
            return 'user' . $id;
        }

        return substr($base, 0, 50);
    }
};
