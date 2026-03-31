<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index', [
            'title' => 'Users',
            'datas' => User::all()
        ]);
    }

    public function create()
    {
        return view('user.create', [
            'title' => 'Users'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->only(['name', 'email', 'role', 'alamat', 'no_telepon']);
            $data['password'] = Hash::make($request->password);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/users'), $filename);
                $data['foto'] = $filename;
            } else {
                $data['foto'] = 'default.png';
            }

            User::create($data);
            return redirect()->route('user.index')->with('simpan', 'User ' . $request->name . ' berhasil disimpan');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Email sudah terdaftar.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan user: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            return view('user.edit', [
                'title' => 'Users',
                'data' => User::findOrFail($id)
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')->with('error', 'User tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->route('user.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->only(['name', 'email', 'role', 'alamat', 'no_telepon']);
            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }
            $user = User::findOrFail($id);

            if ($request->hasFile('foto')) {
                if ($user->foto && $user->foto != 'default.png' && File::exists(public_path('images/users/' . $user->foto))) {
                    File::delete(public_path('images/users/' . $user->foto));
                }
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/users'), $filename);
                $data['foto'] = $filename;
            }

            $user->update($data);
            return redirect()->route('user.index')->with('ubah', 'User ' . $request->name . ' berhasil diupdate');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Email sudah terdaftar.');
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')->with('error', 'User tidak ditemukan.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $nama = $user->name;

            if ($user->foto && $user->foto != 'default.png' && File::exists(public_path('images/users/' . $user->foto))) {
                File::delete(public_path('images/users/' . $user->foto));
            }

            $user->delete();
            return redirect()->route('user.index')->with('hapus', 'User ' . $nama . ' berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')->with('error', 'User tidak ditemukan atau sudah dihapus.');
        } catch (QueryException $e) {
             if ($e->errorInfo[1] == 1451) {
                 return redirect()->route('user.index')->with('error', 'Gagal: User tidak bisa dihapus karena memiliki data transaksi (Order).');
            }
            return redirect()->route('user.index')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->route('user.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
