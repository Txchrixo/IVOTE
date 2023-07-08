<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => Str::uuid(),
                'username' => 'ra',
                'nickname' => 'Registration Authority',
                'password' => '$2y$10$f5KHv0deknv9uzKPSP30s.xwVVtlu0MV7WUDlnCAI1UxifRObCxHi',
                'role' => 1,
                'bitcoin_priv_key' => '',
                'bitcoin_address' => '',
                'eth_priv_key' => '',
                'eth_address' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'username' => 'ea',
                'nickname' => 'Election Authority',
                'password' => '$2y$10$f5KHv0deknv9uzKPSP30s.xwVVtlu0MV7WUDlnCAI1UxifRObCxHi',
                'role' => 2,
                'bitcoin_priv_key' => '93EWiWrdZFJ7PkuhThuw3oxBSvD5UajiStZ7zKgujc9TiUUsd9L',
                'bitcoin_address' => 'n4Kc1AwFos3aZRvD3Tc9imzeMeA8E9DEUr',
                'eth_priv_key' => '',
                'eth_address' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'username' => 'voter',
                'nickname' => NULL,
                'password' => '$2y$10$f5KHv0deknv9uzKPSP30s.xwVVtlu0MV7WUDlnCAI1UxifRObCxHi',
                'role' => 3,
                'bitcoin_priv_key' => '',
                'bitcoin_address' => '',
                'eth_priv_key' => '',
                'eth_address' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}

