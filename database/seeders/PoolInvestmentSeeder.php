<?php

namespace Database\Seeders;

use App\Enums\PoolInvestmentStatus;
use App\Enums\PoolStatus;
use App\Models\Pool;
use App\Models\PoolInvestment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PoolInvestmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pools = $this->seedPools();
        $this->seedInvestments($pools);
    }

    /**
     * Create the investment pools.
     *
     * @return array<string, Pool>
     */
    private function seedPools(): array
    {
        $pools = [
            [
                'name' => 'Growth Fund Alpha',
                'total_amount' => 250000.0,
                'investor_count' => 12,
                'minimum_investment' => 5000.0,
                'status' => PoolStatus::ACTIVE,
            ],
            [
                'name' => 'Stable Income Pool',
                'total_amount' => 180000.0,
                'investor_count' => 8,
                'minimum_investment' => 2500.0,
                'status' => PoolStatus::ACTIVE,
            ],
            [
                'name' => 'Emerging Markets Fund',
                'total_amount' => 95000.0,
                'investor_count' => 5,
                'minimum_investment' => 10000.0,
                'status' => PoolStatus::PAUSED,
            ],
        ];

        $created = [];

        foreach ($pools as $poolData) {
            $pool = Pool::firstOrCreate(
                ['name' => $poolData['name']],
                $poolData,
            );

            $created[$poolData['name']] = $pool;
        }

        return $created;
    }

    /**
     * Create pool investment applications for seeded users.
     *
     * @param  array<string, Pool>  $pools
     */
    private function seedInvestments(array $pools): void
    {
        $users = User::whereIn('email', [
            'muhammad@gmail.com',
            'bigjam@gmail.com',
            'hameed@gmail.com',
            'rajicodes@gmail.com',
        ])
            ->get()
            ->keyBy('email');

        /** @var array<int, array<string, mixed>> */
        $investments = [
            // Muhammad — Active investment in Growth Fund Alpha
            [
                'user_email' => 'muhammad@gmail.com',
                'pool_name' => 'Growth Fund Alpha',
                'full_name' => 'Muhammad Abdullah',
                'phone_number' => '+2348012345678',
                'bank_name' => 'First Bank Nigeria',
                'account_number' => '3012345678',
                'account_name' => 'Muhammad Abdullah',
                'contribution' => 15000.0,
                'share_percentage' => 6.0,
                'payment_proof_path' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&q=80',
                'status' => PoolInvestmentStatus::ACTIVE,
                'terms_accepted' => true,
                'verified_at' => now()->subDays(10),
                'rejection_reason' => null,
            ],

            // Muhammad — Pending investment in Stable Income Pool
            [
                'user_email' => 'muhammad@gmail.com',
                'pool_name' => 'Stable Income Pool',
                'full_name' => 'Muhammad Abdullah',
                'phone_number' => '+2348012345678',
                'bank_name' => 'First Bank Nigeria',
                'account_number' => '3012345678',
                'account_name' => 'Muhammad Abdullah',
                'contribution' => 5000.0,
                'share_percentage' => 0.0,
                'payment_proof_path' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800&q=80',
                'status' => PoolInvestmentStatus::PENDING,
                'terms_accepted' => true,
                'verified_at' => null,
                'rejection_reason' => null,
            ],

            // BigJam — Verified investment in Growth Fund Alpha
            [
                'user_email' => 'bigjam@gmail.com',
                'pool_name' => 'Growth Fund Alpha',
                'full_name' => 'Big Jam Okafor',
                'phone_number' => '+2348023456789',
                'bank_name' => 'GTBank',
                'account_number' => '0123456789',
                'account_name' => 'Big Jam Okafor',
                'contribution' => 25000.0,
                'share_percentage' => 10.0,
                'payment_proof_path' => 'https://images.unsplash.com/photo-1601597111158-2fceff292cdc?w=800&q=80',
                'status' => PoolInvestmentStatus::VERIFIED,
                'terms_accepted' => true,
                'verified_at' => now()->subDays(3),
                'rejection_reason' => null,
            ],

            // BigJam — Rejected investment in Emerging Markets Fund
            [
                'user_email' => 'bigjam@gmail.com',
                'pool_name' => 'Emerging Markets Fund',
                'full_name' => 'Big Jam Okafor',
                'phone_number' => '+2348023456789',
                'bank_name' => 'GTBank',
                'account_number' => '0123456789',
                'account_name' => 'Big Jam Okafor',
                'contribution' => 10000.0,
                'share_percentage' => 0.0,
                'payment_proof_path' => 'https://images.unsplash.com/photo-1571867424488-4565932edb41?w=800&q=80',
                'status' => PoolInvestmentStatus::REJECTED,
                'terms_accepted' => true,
                'verified_at' => null,
                'rejection_reason' => 'Submitted payment receipt does not match the required contribution amount. Please resubmit with a valid proof of payment.',
            ],

            // Hameed — Active investment in Stable Income Pool
            [
                'user_email' => 'hameed@gmail.com',
                'pool_name' => 'Stable Income Pool',
                'full_name' => 'Hameed Balogun',
                'phone_number' => '+2348034567890',
                'bank_name' => 'Access Bank',
                'account_number' => '0987654321',
                'account_name' => 'Hameed Balogun',
                'contribution' => 7500.0,
                'share_percentage' => 4.1667,
                'payment_proof_path' => 'https://images.unsplash.com/photo-1520694478166-daaaaec95b69?w=800&q=80',
                'status' => PoolInvestmentStatus::ACTIVE,
                'terms_accepted' => true,
                'verified_at' => now()->subDays(15),
                'rejection_reason' => null,
            ],

            // Hameed — Pending investment in Emerging Markets Fund
            [
                'user_email' => 'hameed@gmail.com',
                'pool_name' => 'Emerging Markets Fund',
                'full_name' => 'Hameed Balogun',
                'phone_number' => '+2348034567890',
                'bank_name' => 'Access Bank',
                'account_number' => '0987654321',
                'account_name' => 'Hameed Balogun',
                'contribution' => 10000.0,
                'share_percentage' => 0.0,
                'payment_proof_path' => 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=800&q=80',
                'status' => PoolInvestmentStatus::PENDING,
                'terms_accepted' => true,
                'verified_at' => null,
                'rejection_reason' => null,
            ],

            // Raji Codes — Active investment in Growth Fund Alpha
            [
                'user_email' => 'rajicodes@gmail.com',
                'pool_name' => 'Growth Fund Alpha',
                'full_name' => 'Raji Tunde Codes',
                'phone_number' => '+2348045678901',
                'bank_name' => 'Zenith Bank',
                'account_number' => '2109876543',
                'account_name' => 'Raji T. Codes',
                'contribution' => 20000.0,
                'share_percentage' => 8.0,
                'payment_proof_path' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&q=80',
                'status' => PoolInvestmentStatus::ACTIVE,
                'terms_accepted' => true,
                'verified_at' => now()->subDays(20),
                'rejection_reason' => null,
            ],

            // Raji Codes — Pending investment in Stable Income Pool
            [
                'user_email' => 'rajicodes@gmail.com',
                'pool_name' => 'Stable Income Pool',
                'full_name' => 'Raji Tunde Codes',
                'phone_number' => '+2348045678901',
                'bank_name' => 'Zenith Bank',
                'account_number' => '2109876543',
                'account_name' => 'Raji T. Codes',
                'contribution' => 3000.0,
                'share_percentage' => 0.0,
                'payment_proof_path' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800&q=80',
                'status' => PoolInvestmentStatus::PENDING,
                'terms_accepted' => true,
                'verified_at' => null,
                'rejection_reason' => null,
            ],
        ];

        foreach ($investments as $data) {
            $user = $users->get($data['user_email']);
            $pool = $pools[$data['pool_name']] ?? null;

            if (! $user || ! $pool) {
                continue;
            }

            PoolInvestment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'pool_id' => $pool->id,
                    'contribution' => $data['contribution'],
                ],
                [
                    'full_name' => $data['full_name'],
                    'phone_number' => $data['phone_number'],
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['account_number'],
                    'account_name' => $data['account_name'],
                    'share_percentage' => $data['share_percentage'],
                    'payment_proof_path' => $data['payment_proof_path'],
                    'status' => $data['status'],
                    'terms_accepted' => $data['terms_accepted'],
                    'verified_at' => $data['verified_at'],
                    'rejection_reason' => $data['rejection_reason'],
                ],
            );
        }
    }
}
