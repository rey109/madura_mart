<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class OrderController
 * 
 * Handles client orders and reservations.
 * Manages stock reduction upon ordering and ensures data integrity via DB transactions.
 */
class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        return view('order.index', [
            'title' => 'Order',
            'datas' => Order::with(['pelanggan', 'details.product'])->latest()->paginate(5)
        ]);
    }

    /**
     * Show form for creating a new order.
     */
    public function create()
    {
        $title = 'Order';
        $clients = Client::all();
        $products = Product::all();
        $discounts = \App\Models\Discount::active()->get();
        return view('order.create', compact('clients', 'products', 'discounts', 'title'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'no_order' => 'nullable|unique:orders,no_order',
                'tgl_pemesanan' => 'required|date',
                'id_pelanggan' => 'required|exists:clients,id',
                'status_pemesanan' => 'required|in:draft,dipesan,diproses,dikirim,sampai tujuan,diterima,selesai,dibatalkan pembeli,dibatalkan penjual',
                'metode_pembayaran' => 'required|in:tf,cod',
                'products' => 'required|array',
                'products.*' => 'exists:products,id',
                'quantities' => 'required|array',
                'quantities.*' => 'numeric|min:1',
            ]);

            $data = $request->all();
            if (($data['no_order'] ?? '') === '[Auto Generated]') {
                unset($data['no_order']);
            }

            $order = Order::storeAsSet($data);

            return redirect()->route('order.index')->with('simpan', 'Pemesanan berhasil disimpan. Total: Rp ' . number_format($order->total_bayar));

        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Display order details.
     */
    public function show(string $id)
    {
        try {
            $order = Order::with(['pelanggan', 'details.product'])->findOrFail($id);
            return view('order.show', [
                'title' => 'Order Detail',
                'data' => $order
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('order.index')->with('error', 'Data pemesanan tidak ditemukan.');
        }
    }

    /**
     * Show form for editing order.
     */
    public function edit(string $id)
    {
        try {
            return view('order.edit', [
                'title' => 'Order',
                'data' => Order::with('details.product')->findOrFail($id),
                'clients' => Client::all(),
                'products' => Product::all()
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('order.index')->with('error', 'Data pemesanan tidak ditemukan.');
        }
    }

    /**
     * Update order metadata.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'tgl_pemesanan' => 'required|date',
                'id_pelanggan' => 'required|exists:clients,id',
                'status_pemesanan' => 'required|in:draft,dipesan,diproses,dikirim,sampai tujuan,diterima,selesai,dibatalkan pembeli,dibatalkan penjual',
                'metode_pembayaran' => 'required|in:tf,cod',
                'total_bayar' => 'required|numeric'
            ]);
            
            $order = Order::findOrFail($id);
            $order->update($request->only(['tgl_pemesanan', 'id_pelanggan', 'status_pemesanan', 'metode_pembayaran', 'total_bayar', 'keterangan_status']));
            
            return redirect()->route('order.index')->with('ubah', 'Data pemesanan berhasil diupdate.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Delete order and restore stock.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $order = Order::with('details')->findOrFail($id);
            
            foreach ($order->details as $detail) {
                $product = Product::find($detail->id_barang);
                if ($product) {
                    $product->increment('stok', $detail->jumlah_jual);
                }
            }
            
            $order->details()->delete();
            $order->delete();
            
            DB::commit();
            return redirect()->route('order.index')->with('hapus', 'Pemesanan berhasil dihapus.');
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->route('order.index')->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
