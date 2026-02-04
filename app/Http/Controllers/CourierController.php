<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class CourierController extends Controller
{
    public function index()
    {
        return view('courier.index', [
            'title' => 'Courier',
            'datas' => Courier::all()
        ]);
    }

    public function create()
    {
        return view('courier.create', [
            'title' => 'Courier'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->only(['nama_kurir', 'notelepon_kurir', 'plat_kendaraan']);
            Courier::create($data);
            return redirect()->route('courier.index')->with('simpan', 'Data kurir ' . $request->nama_kurir . ' berhasil disimpan');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Data kurir sudah ada (Duplikat).');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            return view('courier.edit', [
                'title' => 'Courier',
                'data' => Courier::findOrFail($id)
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('courier.index')->with('error', 'Data kurir tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->route('courier.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->only(['nama_kurir', 'notelepon_kurir', 'plat_kendaraan']);
            $courier = Courier::findOrFail($id);
            $courier->update($data);
            return redirect()->route('courier.index')->with('ubah', 'Data kurir ' . $request->nama_kurir . ' berhasil diupdate');
        } catch (QueryException $e) {
             if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Data kurir sudah ada (Duplikat).');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->route('courier.index')->with('error', 'Data kurir tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $courier = Courier::findOrFail($id);
            $nama = $courier->nama_kurir;
            $courier->delete();
            return redirect()->route('courier.index')->with('hapus', 'Data kurir ' . $nama . ' berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('courier.index')->with('error', 'Data kurir tidak ditemukan atau sudah dihapus.');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                 return redirect()->route('courier.index')->with('error', 'Gagal: Data tidak bisa dihapus karena masih digunakan di data lain (Pengiriman).');
            }
            return redirect()->route('courier.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->route('courier.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
