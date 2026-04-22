<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Order;
use App\Models\Sale;
use App\Models\VwPurchase;

/**
 * Class ReportController
 * 
 * Generates data for various business reports.
 * Demonstrates the use of Eloquent relationships and Database Views (VwPurchase).
 * 
 * @package App\Http\Controllers
 */
class ReportController extends Controller
{
    /**
     * Display a listing of all distributors as a simple report.
     */
    public function distributor()
    {
        return view('report.distributor', [
            'title' => 'Distributor Reports',
            'datas' => Distributor::all()
        ]);
    }

    /**
     * Display a listing of all products currently in inventory.
     */
    public function product()
    {
        return view('report.product', [
            'title' => 'Product Reports',
            'datas' => Product::all()
        ]);
    }

    /**
     * Detailed Purchase Report using the VwPurchase Database View.
     * This flattened view makes it much easier to list every itemized purchase.
     */
    public function purchase()
    {
        return view('report.purchase', [
            'title' => 'Purchase Reports',
            'datas' => VwPurchase::all()
        ]);
    }

    /**
     * Order Report showing customers and their requested items.
     */
    public function order()
    {
        return view('report.order', [
            'title' => 'Order Reports',
            'datas' => Order::with('pelanggan')->get()
        ]);
    }

    /**
     * Sales Report for revenue analysis.
     */
    public function sale()
    {
        return view('report.sale', [
            'title' => 'Sale Reports',
            'datas' => Sale::all()
        ]);
    }
}

