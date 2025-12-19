<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalTransactions = Transaction::count();
        $totalIncome = Transaction::where('status', 'completed')->sum('total');
        $totalCustomers = User::where('role', 'pelanggan')->count();
        $totalCleaners = User::where('role', 'cleaner')->count();

        $todayTransactions = Transaction::whereDate('transaction_date', Carbon::today())->count();
        $todayIncome = Transaction::where('status', 'completed')
            ->whereDate('transaction_date', Carbon::today())
            ->sum('total');

        // Recent Transactions
        $recentTransactions = Transaction::with(['user', 'service'])
            ->latest()
            ->take(5)
            ->get();

        // Chart: Income Last 7 Days
        $incomeChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $income = Transaction::where('status', 'completed')
                ->whereDate('transaction_date', $date)
                ->sum('total');
            $incomeChart['labels'][] = $date->format('d M');
            $incomeChart['data'][] = $income;
        }

        // Chart: Transaction Status Distribution
        $statusDistribution = Transaction::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        // Ensure all statuses are present for consistency
        $statuses = ['pending', 'process', 'completed', 'cancelled'];
        $statusChart = [];
        foreach ($statuses as $status) {
            $statusChart['labels'][] = ucfirst($status);
            $statusChart['data'][] = $statusDistribution[$status] ?? 0;
        }

        return view('dashboard', compact(
            'totalTransactions',
            'totalIncome',
            'totalCustomers',
            'totalCleaners',
            'todayTransactions',
            'todayIncome',
            'recentTransactions',
            'incomeChart',
            'statusChart'
        ));
    }
}
