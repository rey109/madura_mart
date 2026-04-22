<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SaleController
 * 
 * Handles direct sales transactions.
 * Manages stock reduction and ensures data consistency using database transactions.
 * 
 * @package App\Http\Controllers
 */
class SaleController extends Controller
{
    /**
     * Display a listing of sales transactions.
     */
    public function index()
    {
        return view('sale.index', [
            'title' => 'Sale',
            'datas' => Sale::with('details.product')->latest()->paginate(5)
        ]);
    }

    /**
     * Show form for creating a new sale transaction.
     */
    public function create()
    {
        return view('sale.create', [
            'title' => 'Sale',
            'products' => Product::all()
        ]);
    }

    /**
     * Store a newly created sale in storage.
     * Checks for stock availability before proceeding and uses transactions.
     */
    public function store(Request $request)
    {
        try {
            // Validate Basic Info
            $request->validate([
                'no_struk' => 'required|unique:sales,no_struk',
                'tgl_jual' => 'required|date',
                'products' => 'required|array',
                'products.*' => 'exists:products,id',
                'quantities' => 'required|array',
                'quantities.*' => 'numeric|min:1',
            ]);

            // Atomic 'Header + Details' set saving in the Model
            $sale = Sale::storeAsSet($request->all());

            return redirect()->route('sale.index')->with('simpan', "Penjualan $sale->no_struk berhasil disimpan.");

        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: No Struk sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }


    /**
     * Show form for editing sale metadata.
     */
    public function edit(string $id)
    {
        try {
            return view('sale.edit', [
                'title' => 'Sale',
                'data' => Sale::with('details.product')->findOrFail($id),
                'products' => Product::all()
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('sale.index')->with('error', 'Data penjualan tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->route('sale.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update sale header information.
     * Note: Itemized updates are restricted here to maintain inventory integrity simplified.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $request->only(['no_struk', 'tgl_jual']); 
            $sale = Sale::findOrFail($id);
            $sale->update($data);
            return redirect()->route('sale.index')->with('ubah', 'Data penjualan berhasil diupdate (Detail item tidak berubah).');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: No Struk sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate penjualan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->route('sale.index')->with('error', 'Data penjualan tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Remove a sale and REVERT stock counts.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $sale = Sale::with('details')->findOrFail($id);
            $struk = $sale->no_struk;

            // Restore Stock: Add back what was sold
            foreach ($sale->details as $detail) {
                 $product = Product::find($detail->id_barang);
                 if ($product) {
                     $product->increment('stok', $detail->jumlah_jual);
                 }
            }
            
             // Cleanup details then parent
             $sale->details()->delete();
             $sale->delete();
            
            DB::commit();
            return redirect()->route('sale.index')->with('hapus', 'Penjualan ' . $struk . ' dihapus & stok dikembalikan.');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('sale.index')->with('error', 'Data penjualan tidak ditemukan atau sudah dihapus.');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('sale.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->route('sale.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

