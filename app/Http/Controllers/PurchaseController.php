<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Distributor;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class PurchaseController
 * 
 * Manages product procurement from distributors.
 * Handles stock incrementing and database transactions for data integrity.
 * 
 * @package App\Http\Controllers
 */
class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases with related distributor and product data.
     */
    public function index()
    {
        return view('purchase.index', [
            'title' => 'Purchase',
            'datas' => Purchase::with(['distributor', 'details.product'])->latest()->paginate(5)
        ]);
    }

    /**
     * Show form for new purchase entry.
     */
    public function create()
    {
        return view('purchase.create', [
            'title' => 'Purchase',
            'distributors' => Distributor::all(),
            'products' => Product::all()
        ]);
    }

    /**
     * Store a new purchase.
     * Uses DB Transactions to ensure stock is only increased if the purchase is successfully saved.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'no_nota' => 'nullable|unique:purchases,no_nota',
                'tgl_nota' => 'required|date',
                'id_distributor' => 'required|exists:distributors,id',
                'products' => 'required|array',
                'products.*' => 'exists:products,id',
                'buy_prices' => 'required|array',
                'buy_prices.*' => 'numeric|min:0',
                'quantities' => 'required|array',
                'quantities.*' => 'numeric|min:1',
            ]);

            $data = $request->all();
            if (($data['no_nota'] ?? '') === '[Auto Generated]') {
                unset($data['no_nota']);
            }

            // Delegate logic to the Model for atomic 'Header + Details' set saving
            $purchase = Purchase::storeAsSet($data);

            return redirect()->route('purchase.index')->with('simpan', "Pembelian $purchase->no_nota berhasil disimpan.");

        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: No Nota sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }


    /**
     * Show form for editing purchase metadata (Nota number, Date, Distributor).
     */
    public function edit(string $id)
    {
        try {
            return view('purchase.edit', [
                'title' => 'Purchase',
                'data' => Purchase::with('details.product')->findOrFail($id),
                'distributors' => Distributor::all(),
                'products' => Product::all()
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

    /**
     * Remove a purchase record and revert (decrement) stock.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $purchase = Purchase::with('details')->findOrFail($id);
            $nota = $purchase->no_nota;

            // Delete details first — AFTER DELETE trigger will:
            // 1. Decrement product stock automatically
            // 2. Recalculate total_bayar automatically
            $purchase->details()->delete();
            $purchase->delete();

            DB::commit();
            return redirect()->route('purchase.index')->with('hapus', 'Pembelian ' . $nota . ' dihapus & stok dikurangi.');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('purchase.index')->with('error', 'Data pembelian tidak ditemukan atau sudah dihapus.');
        } catch (QueryException $e) {
            DB::rollBack();
             return redirect()->route('purchase.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->route('purchase.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

