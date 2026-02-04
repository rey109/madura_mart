<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class DeliveryController extends Controller
{
    public function index()
    {
        return view('delivery.index', [
            'title' => 'Delivery',
            'datas' => Delivery::with(['kurir', 'order'])->get()
        ]);
    }

    public function create()
    {
        return view('delivery.create', [
            'title' => 'Delivery',
            'couriers' => Courier::all(),
            'orders' => Order::all()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->only(['tgl_kirim', 'id_kurir', 'id_pemesanan', 'bukti_foto', 'no_invoice']);
            Delivery::create($data);
            return redirect()->route('delivery.index')->with('simpan', 'Pengiriman dengan invoice ' . $request->no_invoice . ' berhasil disimpan');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: No Invoice sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengiriman: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            return view('delivery.edit', [
                'title' => 'Delivery',
                'data' => Delivery::findOrFail($id),
                'couriers' => Courier::all(),
                'orders' => Order::all()
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('delivery.index')->with('error', 'Data pengiriman tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->route('delivery.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->only(['tgl_kirim', 'id_kurir', 'id_pemesanan', 'bukti_foto', 'no_invoice']);
            $delivery = Delivery::findOrFail($id);
            $delivery->update($data);
            return redirect()->route('delivery.index')->with('ubah', 'Pengiriman dengan invoice ' . $request->no_invoice . ' berhasil diupdate');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: No Invoice sudah ada.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate pengiriman: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->route('delivery.index')->with('error', 'Data pengiriman tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $delivery = Delivery::findOrFail($id);
            $invoice = $delivery->no_invoice;
            $delivery->delete();
            return redirect()->route('delivery.index')->with('hapus', 'Pengiriman dengan invoice ' . $invoice . ' berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('delivery.index')->with('error', 'Data pengiriman tidak ditemukan atau sudah dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('delivery.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->route('delivery.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
