<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Distributor;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchase.index', [
            'title' => 'Purchase',
            'datas' => Purchase::with(['distributor', 'details.product'])->latest()->paginate(5)
        ]);
    }

    public function create()
    {
        return view('purchase.create', [
            'title' => 'Purchase',
            'distributors' => Distributor::all(),
            'products' => \App\Models\Products::all()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'no_nota' => 'required|unique:purchases,no_nota',
                'tgl_nota' => 'required|date',
                'id_distributor' => 'required|exists:distributors,id',
                'products' => 'required|array',
                'products.*' => 'exists:products,id',
                'buy_prices' => 'required|array',
                'buy_prices.*' => 'numeric|min:0',
                'quantities' => 'required|array',
                'quantities.*' => 'numeric|min:1',
            ]);

            \Illuminate\Support\Facades\DB::beginTransaction();

            $total_bayar = 0;
            $details = [];

            foreach ($request->products as $index => $productId) {
                $qty = $request->quantities[$index];
                $price = $request->buy_prices[$index];
                $margin = $request->margins[$index] ?? 0;
                
                $product = \App\Models\Products::lockForUpdate()->find($productId);
                if (!$product) {
                    throw new \Exception("Produk ID $productId tidak ditemukan.");
                }

                $subtotal = $price * $qty;
                $total_bayar += $subtotal;

                // Increase Stock
                $product->increment('stok', $qty);

                // Optional: Update selling price based on margin if user wants?
                // For now, just save detail.
                $details[] = [
                    'id_barang' => $productId,
                    'harga_beli' => $price,
                    'margin_jual' => $margin,
                    'jumlah_beli' => $qty,
                    'subtotal' => $subtotal
                ];
            }

            $purchase = Purchase::create([
                'no_nota' => $request->no_nota,
                'tgl_nota' => $request->tgl_nota,
                'id_distributor' => $request->id_distributor,
                'total_bayar' => $total_bayar
            ]);

            foreach ($details as $detail) {
                $purchase->details()->create($detail);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('purchase.index')->with('simpan', 'Pembelian berhasil disimpan. Total: Rp ' . number_format($total_bayar));

        } catch (QueryException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: No Nota sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage());
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
            return view('purchase.edit', [
                'title' => 'Purchase',
                'data' => Purchase::with('details.product')->findOrFail($id),
                'distributors' => Distributor::all(),
                'products' => \App\Models\Products::all()
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('purchase.index')->with('error', 'Data pembelian tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->route('purchase.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->only(['no_nota', 'tgl_nota', 'id_distributor']);
            $purchase = Purchase::findOrFail($id);
            $purchase->update($data);
            return redirect()->route('purchase.index')->with('ubah', 'Data pembelian berhasil diupdate (Detail item tidak berubah).');
        } catch (QueryException $e) {
             if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: No Nota sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate pembelian: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->route('purchase.index')->with('error', 'Data pembelian tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $purchase = Purchase::with('details')->findOrFail($id);
            $nota = $purchase->no_nota;

            // Reduce Stock (Rollback)
            foreach ($purchase->details as $detail) {
                $product = \App\Models\Products::find($detail->id_barang);
                if ($product) {
                    $product->decrement('stok', $detail->jumlah_beli);
                }
            }

            $purchase->details()->delete();
            $purchase->delete();

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('purchase.index')->with('hapus', 'Pembelian ' . $nota . ' dihapus & stok dikurangi.');
        } catch (ModelNotFoundException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('purchase.index')->with('error', 'Data pembelian tidak ditemukan atau sudah dihapus.');
        } catch (QueryException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
             return redirect()->route('purchase.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('purchase.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
