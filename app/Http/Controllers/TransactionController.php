<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\FcmService;

class TransactionController extends Controller
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function index(Request $request)
    {
        // Auto-cancel logic for pending/process transactions older than today
        Transaction::whereIn('status', ['pending', 'process'])
            ->whereDate('transaction_date', '<', Carbon::today())
            ->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'System Cancelled: Expired'
            ]);

        // Upcoming & Today Transactions (Hari ini dan Masa Depan)
        $todayTransactions = Transaction::with(['user', 'cleaner', 'service'])
            ->whereDate('transaction_date', '>=', Carbon::today()) // Ubah jadi >= Today
            ->latest() // Urutkan dari yang paling baru dibuat
            ->paginate(10, ['*'], 'today_page');

        // History Transactions (Past Transactions)
        $historyQuery = Transaction::with(['user', 'cleaner', 'service'])
            ->whereDate('transaction_date', '<', Carbon::today()); // Hanya masa lalu

        // Filter by Date
        if ($request->filled('filter_date')) {
            $historyQuery->whereDate('transaction_date', $request->filter_date);
        }

        // Filter by Service
        if ($request->filled('filter_service')) {
            $historyQuery->where('service_id', $request->filter_service);
        }

        // Filter by Status
        if ($request->filled('filter_status')) {
            $historyQuery->where('status', $request->filter_status);
        }

        // Filter by Cleaner
        if ($request->filled('filter_cleaner')) {
            $historyQuery->where('cleaner_id', $request->filter_cleaner);
        }

        $historyTransactions = $historyQuery->latest()
            ->paginate(10, ['*'], 'history_page');
            
        // Append query parameters to pagination links
        $historyTransactions->appends($request->all());

        $cleaners = User::where('role', 'cleaner')->get();
        $services = Service::all();

        return view('transaksi.index', compact('todayTransactions', 'historyTransactions', 'cleaners', 'services'));
    }

    public function show(Transaction $transaction)
    {
        return response()->json($transaction->load(['user', 'cleaner', 'service']));
    }

    public function assign(Request $request, Transaction $transaction)
    {
        $request->validate([
            'cleaner_id' => 'required|exists:users,id',
        ]);
        
        $transaction->update([
            'cleaner_id' => $request->cleaner_id,
            'status' => 'process'
        ]);
        
        // Load relationships needed for notification
        $transaction->load(['user', 'cleaner']);

        // Send Notification
        if ($transaction->user && $transaction->user->device_token) {
            $this->fcmService->sendToToken(
                $transaction->user->device_token,
                'Pesanan Diproses',
                'Cleaner ' . ($transaction->cleaner->name ?? 'Petugas') . ' telah ditugaskan untuk pesanan Anda.',
                [
                    'transaction_id' => (string) $transaction->id,
                    'status' => 'process',
                    'type' => 'transaction_update'
                ]
            );
        }
        
        return response()->json(['success' => true]);
    }

    public function complete(Transaction $transaction)
    {
        if ($transaction->status !== 'process') {
            return response()->json(['error' => 'Hanya transaksi berstatus proses yang dapat diselesaikan'], 400);
        }

        $transaction->update([
            'status' => 'completed'
        ]);

        // Send Notification
        if ($transaction->user && $transaction->user->device_token) {
            $this->fcmService->sendToToken(
                $transaction->user->device_token,
                'Pesanan Selesai',
                'Pesanan Anda telah selesai dikerjakan. Terima kasih!',
                [
                    'transaction_id' => (string) $transaction->id,
                    'status' => 'completed',
                    'type' => 'transaction_update'
                ]
            );
        }
        
        return response()->json(['success' => true]);
    }

    public function cancel(Request $request, Transaction $transaction)
    {
        if (!in_array($transaction->status, ['pending', 'process'])) {
            return response()->json(['error' => 'Transaksi tidak dapat dibatalkan'], 400);
        }

        $request->validate([
            'reason' => 'required|string|min:5'
        ]);

        $transaction->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'Admin Cancelled: ' . $request->reason
        ]);

        // Send Notification
        if ($transaction->user && $transaction->user->device_token) {
            $this->fcmService->sendToToken(
                $transaction->user->device_token,
                'Pesanan Dibatalkan',
                'Pesanan Anda dibatalkan oleh admin. Alasan: ' . $request->reason,
                [
                    'transaction_id' => (string) $transaction->id,
                    'status' => 'cancelled',
                    'type' => 'transaction_update'
                ]
            );
        }
        
        return response()->json(['success' => true]);
    }
}
