<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Service;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestBriTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:bri-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test creating a BRI transaction via PaymentController';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting BRI Transaction Test...');

        // 1. Get a User (Customer)
        $user = User::where('role', 'pelanggan')->first();
        if (!$user) {
            $user = User::first(); // Fallback
            $this->warn('No "pelanggan" found, using first user: ' . $user->name);
        } else {
            $this->info('Using User: ' . $user->name);
        }

        // 2. Get a Cleaner
        $cleaner = User::where('role', 'cleaner')->first();
        if (!$cleaner) {
             $cleaner = User::where('id', '!=', $user->id)->first(); // Fallback
             if (!$cleaner) $cleaner = $user; // Last resort
             $this->warn('No "cleaner" found, using user: ' . $cleaner->name);
        } else {
            $this->info('Using Cleaner: ' . $cleaner->name);
        }

        // 3. Get a Service
        $service = Service::first();
        if (!$service) {
            $this->error('No Services found! Please seed services first.');
            return;
        }
        $this->info('Using Service: ' . $service->name . ' (Price: ' . $service->price . ')');

        // 4. Authenticate User
        Auth::login($user);

        // 5. Mock Request
        $request = Request::create('/api/payment/create', 'POST', [
            'service_id' => $service->id,
            'cleaner_id' => $cleaner->id,
            'transaction_date' => now()->addDay()->format('Y-m-d'),
            'payment_gateway' => 'bri'
        ]);

        // 6. Call Controller
        try {
            $controller = app(PaymentController::class);
            $response = $controller->createTransaction($request);

            $content = $response->getContent();
            $data = json_decode($content, true);

            if ($response->status() == 200) {
                $this->info('------------------------------------------------');
                $this->info('Transaction Created Successfully!');
                $this->info('Transaction Code: ' . $data['data']['code']);
                $this->info('Total Amount: ' . $data['data']['total']);
                $this->info('Payment Gateway: ' . $data['payment_gateway']);
                $this->info('Payment Token (TrxId): ' . $data['payment_token']);
                $this->info('Virtual Account No: ' . $data['payment_url']);
                $this->info('------------------------------------------------');
            } else {
                $this->error('Transaction Failed!');
                $this->error('Status: ' . $response->status());
                $this->error('Response: ' . $content);
            }

        } catch (\Exception $e) {
            $this->error('Exception Occurred: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
