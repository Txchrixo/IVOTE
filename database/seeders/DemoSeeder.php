<?php

namespace Database\Seeders;

use App\Enums\ElectionStage;
use App\Enums\Role;
use App\Models\Candidate;
use App\Models\Code;
use App\Models\Key;
use App\Models\User;
use App\Models\VoteList;
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use RuntimeException;

/**
 * Fills an empty database with a realistic election season, for demos and screenshots.
 *
 *   php artisan migrate:fresh --seed --seeder=DemoSeeder
 *
 * Passwords and 2FA secrets come from DEMO_PASSWORD / DEMO_2FA_SECRET, or are generated
 * and printed once. Refuses to run in production.
 */
class DemoSeeder extends Seeder
{
    private const ELECTIONS = [
        [
            'title' => 'Student Union Election 2023', 'created' => '2023-05-29 11:20',
            'description' => 'Election of the president and board of the IUT Douala student union for the 2023-2024 academic year.',
            'stage' => ElectionStage::Done, 'bg' => 'bg-1.jpg', 'number' => 180, 'paid' => 180, 'registered' => 162,
            'veriff' => ['2023-06-05 08:00', '2023-06-12 18:00'], 'vote' => ['2023-06-19 08:00', '2023-06-20 18:00'],
            'candidates' => [
                ['Aïcha Mbarga', 'Software Engineering, L3. Digital student services and a 24/7 library.'],
                ['Jean-Paul Ndongo', 'Electrical Engineering, L3. Better transport and sports facilities.'],
                ['Clarisse Tchoua', 'Business Management, L2. Internship network with local companies.'],
            ],
        ],
        [
            'title' => 'Engineering Class Delegates 2023', 'created' => '2023-07-03 09:12',
            'description' => 'Class delegates for the first-year engineering cohorts.',
            'stage' => ElectionStage::Ongoing, 'bg' => 'bg-3.jpg', 'number' => 64, 'paid' => 64, 'registered' => 58,
            'veriff' => ['2023-07-10 08:00', '2023-07-17 18:00'], 'vote' => ['2023-07-24 08:00', '2023-07-26 18:00'],
            'candidates' => [
                ['Boris Eyenga', 'Weekly office hours with the department head.'],
                ['Michelle Ngo Bassa', 'Shared course notes and a tutoring programme.'],
                ['Hervé Kamga', 'Lab access in the evening for project teams.'],
                ['Sandrine Fotso', 'Transparent use of the class budget.'],
            ],
        ],
        [
            'title' => 'Staff Representatives 2023', 'created' => '2023-07-10 14:37',
            'description' => 'Staff representatives on the CODEC works council.',
            'stage' => ElectionStage::Waiting, 'bg' => 'bg-5.jpg', 'number' => 40, 'paid' => 12, 'registered' => 37,
            'veriff' => ['2023-07-17 08:00', '2023-07-21 18:00'], 'vote' => ['2023-08-02 08:00', '2023-08-03 18:00'],
            'candidates' => [
                ['Emmanuel Nkoulou', 'Training budget for every employee.'],
                ['Pauline Essomba', 'Flexible hours and remote work days.'],
                ['Rodrigue Tagne', 'Health insurance review.'],
            ],
        ],
        [
            'title' => 'Coding Club Board 2023', 'created' => '2023-07-14 10:05',
            'description' => 'President, secretary and treasurer of the software engineering club.',
            'stage' => ElectionStage::Veriff, 'bg' => 'bg-7.jpg', 'number' => 0, 'paid' => 0, 'registered' => 21,
            'veriff' => ['2023-07-20 08:00', '2023-07-31 18:00'], 'vote' => ['2023-08-07 08:00', '2023-08-08 18:00'],
            'candidates' => [
                ['Yannick Mballa', 'Monthly hackathons and open-source sprints.'],
                ['Grace Atangana', 'Mentoring between L1 and L3 students.'],
                ['Stéphane Djoumessi', 'Partnerships with Douala tech start-ups.'],
            ],
        ],
        [
            'title' => 'Alumni Council 2023', 'created' => '2023-07-21 16:48',
            'description' => 'Council of the IUT Douala alumni association.',
            'stage' => ElectionStage::Initial, 'bg' => 'bg-9.jpg', 'number' => 0, 'paid' => 0, 'registered' => 0,
            'veriff' => null, 'vote' => null,
            'candidates' => [
                ['Christelle Ewane', 'A yearly alumni forum in Douala.'],
                ['Patrick Mvondo', 'Scholarships funded by alumni.'],
            ],
        ],
    ];

    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('The demo seeder never runs in production.');
        }

        $password = env('DEMO_PASSWORD') ?: Str::password(16);
        $secret = env('DEMO_2FA_SECRET') ?: (new Google2FA())->generateSecretKey();

        $wallet = $this->wallet();
        $this->account(Role::EA, 'ea', 'Electoral Commission', $password, $secret, $wallet);
        $this->account(Role::RA, 'ra', 'Registration Office', $password, $secret);

        foreach (self::ELECTIONS as $data) {
            $this->election($data);
        }

        $this->command?->info('Demo data created. Accounts: ea / ra.');
        if (!env('DEMO_PASSWORD')) {
            $this->command?->warn("Password: {$password}");
        }
        if (!env('DEMO_2FA_SECRET')) {
            $this->command?->warn("2FA secret: {$secret}");
        }
    }

    private function account(Role $role, string $username, string $nickname, string $password, string $secret, ?array $wallet = null): void
    {
        $user = User::firstOrNew(['username' => $username]);
        $user->fill(['nickname' => $nickname, 'password' => Hash::make($password)]);
        $user->role = $role;
        if ($wallet) {
            $user->bitcoin_address = $wallet['address'];
            $user->bitcoin_priv_key = $wallet['wif'];
            $user->eth_address = '0x' . bin2hex(random_bytes(20));
            $user->solana_address = self::base58(random_bytes(32));
        }
        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => collect(range(1, 8))->map(fn () => Str::random(10) . '-' . Str::random(10))->all(),
            'two_factor_confirmed_at' => now(),
        ])->save();
    }

    private function election(array $data): void
    {
        $election = new VoteList();
        $election->timestamps = false;
        $election->forceFill(['created_at' => $data['created'], 'updated_at' => $data['created']])->fill([
            'title' => $data['title'],
            'number' => $data['number'] ?: $data['registered'],
            'description' => $data['description'],
            'stage' => $data['stage']->value,
            'bg' => $data['bg'],
            'start_veriff' => $data['veriff'][0] ?? null,
            'end_veriff' => $data['veriff'][1] ?? null,
            'start_vote' => $data['vote'][0] ?? null,
            'end_vote' => $data['vote'][1] ?? null,
        ])->save();

        foreach ($data['candidates'] as [$name, $program]) {
            Candidate::create(['name' => $name, 'des' => $program, 'vote_id' => $election->id]);
        }

        // Ballot codes; voters who generated their keys have a registered public key
        for ($i = 0; $i < $data['registered']; $i++) {
            Code::create([
                'code' => strtoupper(Str::random(16)),
                'is_used' => $data['stage'] === ElectionStage::Done && $i < $data['registered'] - 9,
                // Compressed secp256k1 public key, the ring member used by the linkable ring signature
                'public_key' => $data['stage']->value >= ElectionStage::Waiting->value ? $this->wallet()['public'] : '0',
                'item_id' => $election->id,
            ]);
        }

        // Voter address pool funded by the EA (OP_RETURN hash of the voter key)
        for ($i = 0; $i < $data['number']; $i++) {
            $wallet = $this->wallet();
            $key = new Key([
                'bitcoin_private_key' => $wallet['wif'],
                'bitcoin_public_key' => $wallet['public'],
                'bitcoin_address' => $wallet['address'],
                'used_for' => $election->id,
            ]);
            $key->forceFill([
                'is_paid' => $i < $data['paid'],
                'is_used' => $data['stage'] === ElectionStage::Done && $i < $data['registered'] - 9,
            ])->save();
        }
    }

    /** Base58 (Bitcoin alphabet), the encoding of Solana addresses. */
    private static function base58(string $bytes): string
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $number = gmp_import($bytes);
        $encoded = '';
        while (gmp_cmp($number, 0) > 0) {
            [$number, $remainder] = gmp_div_qr($number, 58);
            $encoded = $alphabet[gmp_intval($remainder)] . $encoded;
        }

        return str_repeat('1', strlen($bytes) - strlen(ltrim($bytes, "\0"))) . $encoded;
    }

    /** @return array{wif: string, public: string, address: string} a testnet key pair */
    private function wallet(): array
    {
        $ecdsa = new BitcoinECDSA();
        $ecdsa->setNetworkPrefix('6f');
        $ecdsa->generateRandomPrivateKey();

        return ['wif' => $ecdsa->getWif(), 'public' => $ecdsa->getPubKey(), 'address' => $ecdsa->getAddress()];
    }
}
