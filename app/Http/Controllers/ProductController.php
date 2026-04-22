<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Class ProductController
 * 
 * Manages product inventory, including soft deletes and image lifecycle management.
 * Includes a 'Recycle Bin' feature for product images.
 * 
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * Display a listing of active products.
     */
    public function index()
    {
        return view('product.index', [
            'title' => 'Products',
            'datas' => Product::paginate(50)
        ]);
    }

    /**
     * Show form for creating a new product.
     */
    public function create()
    {
        return view('product.create', [
            'title' => 'Products'
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'foto_barang' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $data = $request->only(['kd_barang', 'nama_barang', 'jenis_barang', 'tgl_expired', 'harga_jual', 'stok']);
            
            // Handle file upload: Store in 'public/images/products'
            if ($request->hasFile('foto_barang')) {
                $file = $request->file('foto_barang');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images/products', $filename, 'public');
                $data['foto_barang'] = $path;
            }
            
            Product::create($data);
            return redirect()->route('product.index')->with('simpan', 'Produk ' . $request->nama_barang . ' berhasil disimpan');
        } catch (QueryException $e) {
            // Unique constraint error (e.g., duplicated barcode/kd_barang)
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Kode Barang atau Nama Produk sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            return view('product.edit', [
                'title' => 'Products',
                'data' => Product::findOrFail($id)
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('product.index')->with('error', 'Produk tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->route('product.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified product in storage.
     * Moves old images to a recycle bin before replacing them.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'foto_barang' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $data = $request->only(['kd_barang', 'nama_barang', 'jenis_barang', 'tgl_expired', 'harga_jual', 'stok']);
            $product = Product::findOrFail($id);
            
            // If new file provided, move old one to Recycle Bin
            if ($request->hasFile('foto_barang')) {
                if ($product->foto_barang && Storage::disk('public')->exists($product->foto_barang)) {
                    if (!Storage::disk('public')->exists('images/recycle_bin')) {
                         Storage::disk('public')->makeDirectory('images/recycle_bin');
                    }
                    $filename = basename($product->foto_barang);
                    // Move to trash folder instead of permanent deletion
                    Storage::disk('public')->move($product->foto_barang, 'images/recycle_bin/' . time() . '_' . $filename);
                }
                
                $file = $request->file('foto_barang');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images/products', $filename, 'public');
                $data['foto_barang'] = $path;
            }
            
            $product->update($data);
            return redirect()->route('product.index')->with('ubah', 'Produk ' . $request->nama_barang . ' berhasil diupdate');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Kode Barang atau Nama Produk sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate produk: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->route('product.index')->with('error', 'Produk tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Move product to Trash (Soft Delete).
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $nama = $product->nama_barang;
            
            // For Soft Deletes, we DO NOT move the file yet.
            // The file stays in place so it can be shown in the Trash view.
            
            $product->delete();
            return redirect()->route('product.index')->with('hapus', 'Produk ' . $nama . ' berhasil dipindahkan ke Sampah');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('product.index')->with('error', 'Produk tidak ditemukan atau sudah dihapus.');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                 return redirect()->route('product.index')->with('error', 'Gagal: Produk tidak bisa dihapus karena sudah ada transaksi.');
            }
            return redirect()->route('product.index')->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->route('product.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of soft-deleted products.
     */
    public function trash()
    {
        return view('product.trash', [
            'title' => 'Product Trash',
            'datas' => Product::onlyTrashed()->paginate(50)
        ]);
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore(string $id)
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();
            return redirect()->route('product.trash')->with('restore', 'Produk ' . $product->nama_barang . ' berhasil dikembalikan');
        } catch (Throwable $e) {
            return redirect()->route('product.trash')->with('error', 'Gagal restore: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a product and its image.
     */
    public function forceDelete(string $id)
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            $nama = $product->nama_barang;

            // Final Backup: Move image to recycle_bin before permanent deletion
            if ($product->foto_barang && Storage::disk('public')->exists($product->foto_barang)) {
                if (!Storage::disk('public')->exists('images/recycle_bin')) {
                        Storage::disk('public')->makeDirectory('images/recycle_bin');
                }
                $filename = basename($product->foto_barang);
                Storage::disk('public')->move($product->foto_barang, 'images/recycle_bin/' . time() . '_' . $filename);
            }
            
            $product->forceDelete();
            return redirect()->route('product.trash')->with('hapus', 'Produk ' . $nama . ' dihapus permanen');
        } catch (Throwable $e) {
            return redirect()->route('product.trash')->with('error', 'Gagal hapus permanen: ' . $e->getMessage());
        }
    }
}

