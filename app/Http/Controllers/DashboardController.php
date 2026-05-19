<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * Class DashboardController
 * 
 * Handles the main dashboard view and statistics calculation.
 * 
 * @package App\Http\Controllers
 */
class DashboardController extends Controller

{
    public function index()
    {
        $today = date('Y-m-d');
        
        // Today's Money (Total Sales Today)
        $todaysMoney = Sale::whereDate('tgl_jual', $today)->sum('total_bayar');
        
        // Today's Transactions (Count of Sales Today)
        $todaysTransactions = Sale::whereDate('tgl_jual', $today)->count();
        
        // Total Clients
        $totalClients = Client::count();
        
        // Total Sales (Lifetime)
        $totalSales = Sale::sum('total_bayar');

        // Recent Orders (Limit 5)
        $recentOrders = Order::with('pelanggan')->latest('tgl_pemesanan')->take(5)->get();

        // Today's Profit (Revenue - COGS)
        $todaysSales = Sale::with('details')->whereDate('tgl_jual', $today)->get();
        $todaysProfit = 0;
        foreach ($todaysSales as $sale) {
            $todaysProfit += $sale->details->sum('margin');
        }

        // --- CHART DATA ---
        
        // 1. Weekly Activity (Last 7 Days)
        $weeklyData = [];
        $weeklyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayName = date('D', strtotime($date));
            $weeklyLabels[] = $dayName;
            $weeklyData[] = Sale::whereDate('tgl_jual', $date)->sum('total_bayar');
        }

        // 2. Sales Overview (Monthly for Current Year)
        $monthlyData = [];
        $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $currentYear = date('Y');
        foreach (range(1, 12) as $month) {
            $monthlyData[] = Sale::whereYear('tgl_jual', $currentYear)
                                ->whereMonth('tgl_jual', $month)
                                ->sum('total_bayar');
        }

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'todaysMoney' => $todaysMoney,
            'todaysTransactions' => $todaysTransactions,
            'todaysProfit' => $todaysProfit,
            'totalClients' => $totalClients,
            'totalSales' => $totalSales,
            'recentOrders' => $recentOrders,
            'weeklyLabels' => $weeklyLabels,
            'weeklyData' => $weeklyData,
            'monthlyLabels' => $monthlyLabels,
            'monthlyData' => $monthlyData
        ]);
    }
}
