<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\Products;
use App\Models\Purchase;
use App\Models\Order;
use App\Models\Sale;

class ReportController extends Controller
{
    public function distributor()
    {
        return view('report.distributor', [
            'title' => 'Distributor Reports',
            'datas' => Distributor::all()
        ]);
    }

    public function product()
    {
        return view('report.product', [
            'title' => 'Product Reports',
            'datas' => Products::all()
        ]);
    }

    public function purchase()
    {
        return view('report.purchase', [
            'title' => 'Purchase Reports',
            'datas' => Purchase::with('distributor')->get()
        ]);
    }

    public function order()
    {
        return view('report.order', [
            'title' => 'Order Reports',
            'datas' => Order::with('pelanggan')->get()
        ]);
    }

    public function sale()
    {
        return view('report.sale', [
            'title' => 'Sale Reports',
            'datas' => Sale::all()
        ]);
    }
}
