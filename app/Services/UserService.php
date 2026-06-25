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

        Cache::forever('sac_username_counter', $nextNum);

        return $nextNum;
    }

    public function generateSacUsername(): string
    {
        return DB::transaction(function () {
            $counter = AvailableUsername::where('key', 'sac_username')
                ->lockForUpdate()
                ->firstOrFail();

            $number = $counter->value;

            if ($number > 999) {
                throw new \RuntimeException('Stock Username SAC sudah habis.');
            }

            $counter->update([
                'value' => $number + 1
            ]);

            return 'SAC' . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }

    private function findAvailableNum(): int
    {
        $used = [];

        User::where('name', 'LIKE', 'SAC%')
            ->pluck('name')
            ->each(function ($name) use (&$used) {
                $num = (int) substr($name, 3);

                if ($num >= 100 && $num <= 999) {
                    $used[$num] = true;
                }
            });

        TempUser::select('data')
            ->cursor()
            ->each(function ($temp) use (&$used) {

                $data = json_decode($temp->data, true);

                $username = $data['username'] ?? null;

                if (
                    $username &&
                    preg_match('/^SAC(\d{3})$/', $username, $match)
                ) {
                    $used[(int) $match[1]] = true;
                }
            });

        for ($i = 100; $i <= 999; $i++) {
            if (! isset($used[$i])) {
                return $i;
            }
        }

        throw new \RuntimeException(
            'Semua username SAC100-SAC999 sudah terpakai.'
        );
    }
}
