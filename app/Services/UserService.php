<?php

namespace App\Services;

use App\Models\AvailableUsername;
use App\Models\TempUser;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function initUsernameCounter(): int
    {
        $nextNum = $this->findAvailableNum();

        AvailableUsername::updateOrCreate(
            ['key' => 'sac_username'],
            ['value' => $nextNum]
        );

        Cache::forever('sac_username_counter', $nextNum);

        return $nextNum;
    }

    public function generateSacUsername(): string
    {
        return DB::transaction(function () {
            $counter = AvailableUsername::where('key', 'sac_username')
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = AvailableUsername::create([
                    'key' => 'sac_username',
                    'value' => $this->findAvailableNum(),
                ]);
            }

            $number = $this->findAvailableNum();
            $nextNumber = $this->findAvailableNum($number + 1, false);

            $counter->update([
                'value' => $nextNumber ?? 1000,
            ]);

            return 'SAC' . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }

    private function findAvailableNum(int $start = 100, bool $throwWhenFull = true): ?int
    {
        $used = [];

        User::withTrashed()
            ->where('name', 'LIKE', 'SAC%')
            ->pluck('name')
            ->each(function ($name) use (&$used) {
                if (preg_match('/^SAC(\d{3})$/i', $name, $match)) {
                    $used[(int) $match[1]] = true;
                }
            });

        TempUser::select('data')
            ->cursor()
            ->each(function ($temp) use (&$used) {

                $username = $this->extractTempUsername($temp->data);

                if (
                    $username &&
                    preg_match('/^SAC(\d{3})$/i', $username, $match)
                ) {
                    $used[(int) $match[1]] = true;
                }
            });

        for ($i = max(100, $start); $i <= 999; $i++) {
            if (! isset($used[$i])) {
                return $i;
            }
        }

        if (! $throwWhenFull) {
            return null;
        }

        throw new \RuntimeException(
            'Semua username SAC100-SAC999 sudah terpakai.'
        );
    }

    private function extractTempUsername(string $rawData): ?string
    {
        $data = json_decode($rawData, true);

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (! is_array($data)) {
            $json = trim($rawData, "\" \n\r\t");
            $json = preg_replace('/^"+\s*{/', '{', $json);
            $json = preg_replace('/}\s*"+$/', '}', $json);
            $data = json_decode(stripslashes($json), true);
        }

        return is_array($data) ? ($data['username'] ?? null) : null;
    }
}
