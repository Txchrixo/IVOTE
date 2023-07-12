<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Chiffre les clés privées de l'EA encore stockées en clair (avant l'ajout du cast `encrypted`).
| Idempotent : une valeur déjà chiffrée est ignorée.
*/
Artisan::command('ivote:encrypt-private-keys', function () {
    $columns = ['bitcoin_priv_key', 'eth_priv_key', 'solana_priv_key'];
    $count = 0;

    \Illuminate\Support\Facades\DB::table('users')->orderBy('id')->each(function ($row) use ($columns, &$count) {
        $updates = [];
        foreach ($columns as $column) {
            $value = $row->{$column} ?? null;
            if ($value === null || $value === '') {
                continue;
            }
            try {
                \Illuminate\Support\Facades\Crypt::decryptString($value);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                $updates[$column] = \Illuminate\Support\Facades\Crypt::encryptString($value);
            }
        }
        if ($updates) {
            \Illuminate\Support\Facades\DB::table('users')->where('id', $row->id)->update($updates);
            $count += count($updates);
        }
    });

    $this->info("{$count} clé(s) chiffrée(s).");
})->purpose('Chiffre au repos les clés privées EA stockées en clair');
