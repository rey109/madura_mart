<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Client;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class OrderController extends Controller
{
    public function index()
    {
        return view('order.index', [
            'title' => 'Order',
            'datas' => Order::with(['pelanggan', 'details.product'])->latest()->paginate(5)
        ]);
    }

    public function create()
    {
        return view('order.create', [
            'title' => 'Order',
            'clients' => Client::all(),
            'products' => \App\Models\Products::all()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'tgl_pemesanan' => 'required|date',
                'id_pelanggan' => 'required|exists:clients,id',
                'status_pemesanan' => 'required|in:draft,dipesan,diproses,dikirim,sampai tujuan,diterima,selesai,dibatalkan pembeli,dibatalkan penjual',
                'metode_pembayaran' => 'required|in:tf,cod',
                'products' => 'required|array',
                'products.*' => 'exists:products,id',
                'quantities' => 'required|array',
                'quantities.*' => 'numeric|min:1',
            ]);

            \Illuminate\Support\Facades\DB::beginTransaction();

            $total_bayar = 0;
            $details = [];

            foreach ($request->products as $index => $productId) {
                $qty = $request->quantities[$index];
                $product = \App\Models\Products::lockForUpdate()->find($productId);
                
                if (!$product) {
                    throw new \Exception("Produk ID $productId tidak ditemukan.");
                }

                if ($product->stok < $qty) {
                    throw new \Exception("Stok tidak cukup untuk produk: " . $product->nama_barang . " (Sisa: " . $product->stok . ")");
                }

                $subtotal = $product->harga_jual * $qty;
                $total_bayar += $subtotal;

                // Decrease Stock (Reserve for Order)
                $product->decrement('stok', $qty);

                $details[] = [
                    'id_barang' => $productId,
                    'harga_jual' => $product->harga_jual,
                    'jumlah_jual' => $qty,
                    'subtotal' => $subtotal,
                    'catatan' => $request->item_notes[$index] ?? null
                ];
            }

            $order = Order::create([
                'tgl_pemesanan' => $request->tgl_pemesanan,
                'id_pelanggan' => $request->id_pelanggan,
                'status_pemesanan' => $request->status_pemesanan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_bayar' => $total_bayar,
                'status_catatan' => $request->status_catatan
            ]);

            foreach ($details as $detail) {
                $order->details()->create($detail);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('order.index')->with('simpan', 'Pemesanan berhasil disimpan. Total: Rp ' . number_format($total_bayar));

        } catch (QueryException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pemesanan: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            return view('order.edit', [
                'title' => 'Order',
                'data' => Order::with('details.product')->findOrFail($id),
                'clients' => Client::all(),
                'products' => \App\Models\Products::all()
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('order.index')->with('error', 'Data pemesanan tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->route('order.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

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
            
            $data = $request->only(['tgl_pemesanan', 'id_pelanggan', 'status_pemesanan', 'metode_pembayaran', 'total_bayar', 'keterangan_status']);
            $order = Order::findOrFail($id);
            $order->update($data);
            return redirect()->route('order.index')->with('ubah', 'Data pemesanan berhasil diupdate (Detail item tidak berubah).');
        } catch (QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate pemesanan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->route('order.index')->with('error', 'Data pemesanan tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            $order = Order::with('details')->findOrFail($id);
            
            // Restore Stock
            foreach ($order->details as $detail) {
                $product = \App\Models\Products::find($detail->id_barang);
                if ($product) {
                    $product->increment('stok', $detail->jumlah_jual);
                }
            }
            
            $order->details()->delete();
            $order->delete();
            
            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('order.index')->with('hapus', 'Pemesanan berhasil dihapus & stok dikembalikan.');
        } catch (ModelNotFoundException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('order.index')->with('error', 'Data pemesanan tidak ditemukan atau sudah dihapus.');
        } catch (QueryException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('order.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('order.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
