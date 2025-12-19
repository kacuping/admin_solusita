<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing transactions
        Transaction::truncate();

        $user = User::where('role', 'pelanggan')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Pelanggan Dummy',
                'email' => 'pelanggan@dummy.com',
                'password' => bcrypt('password'),
                'role' => 'pelanggan',
            ]);
        }

        $services = Service::all();
        if ($services->isEmpty()) {
            $this->command->error('No services found. Please seed services first.');
            return;
        }

        $statuses = ['pending', 'process', 'completed', 'cancelled'];

        // Generate transactions for today and past 30 days
        for ($i = 1; $i <= 50; $i++) {
            $date = Carbon::today()->subDays(rand(0, 30));
            $service = $services->random();
            $status = $statuses[array_rand($statuses)];
            
            // If status is cancelled, provide a reason
            $cancellationReason = $status === 'cancelled' ? 'Dibatalkan oleh sistem atau pengguna' : null;

            // If status is process or completed, assign a cleaner if available
            $cleanerId = null;
            if (in_array($status, ['process', 'completed'])) {
                $cleaner = User::where('role', 'cleaner')->inRandomOrder()->first();
                $cleanerId = $cleaner ? $cleaner->id : null;
            }

            // Generate dummy rating for completed transactions
            $rating = null;
            $review = null;
            if ($status === 'completed') {
                $rating = rand(3, 5);
                $reviews = [
                    'Sangat puas dengan layanannya!',
                    'Bersih dan rapi, terima kasih.',
                    'Pelayanan oke, tapi datang agak telat.',
                    'Luar biasa, akan pesan lagi.',
                    'Cukup baik.'
                ];
                $review = $reviews[array_rand($reviews)];
            }

            Transaction::create([
                'code' => 'TRX-' . $date->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'service_id' => $service->id,
                'transaction_date' => $date,
                'status' => $status,
                'total' => $service->price, // Use service price as total for simplicity
                'cleaner_id' => $cleanerId,
                'cancellation_reason' => $cancellationReason,
                'rating' => $rating,
                'review' => $review
            ]);
        }
    }
}
