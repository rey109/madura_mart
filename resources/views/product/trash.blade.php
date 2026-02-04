@extends('be.master')

@section('menu')
    @include('be.menu')
@endsection

@section('product')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Product Recycle Bin (Trash)</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="d-flex justify-content-between mx-3">
                        <a href="{{ route('product.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Products
                        </a>
                    </div>
                    
                    @if(session()->has('restore'))
                    <div class="alert alert-success alert-dismissible fade show text-white mx-3" role="alert">
                        <span class="alert-icon align-middle">
                          <span class="material-icons text-md">thumb_up_off_alt</span>
                        </span>
                        <span class="alert-text"><strong>Berhasil!</strong> {{ session('restore') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session()->has('hapus'))
                    <div class="alert alert-danger alert-dismissible fade show text-white mx-3" role="alert">
                        <span class="alert-icon align-middle">
                          <span class="material-icons text-md">delete</span>
                        </span>
                        <span class="alert-text"><strong>Dihapus!</strong> {{ session('hapus') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Foto</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Kode</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Nama Barang</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Deleted At</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($datas as $nmr => $data)
                                <tr>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0 ps-3">{{ $datas->firstItem() + $nmr }}</p>
                                    </td>
                                    <td>
                                        @if($data->foto_barang)
                                            <img src="{{ asset('storage/' . $data->foto_barang) }}" 
                                                 alt="{{ $data->nama_barang }}" 
                                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                                        @else
                                            <div style="width: 80px; height: 80px; background: #e9ecef; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image text-secondary" style="font-size: 24px;"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $data->kd_barang }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $data->nama_barang }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $data->deleted_at->format('d M Y H:i') }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('product.restore', $data->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Restore data ini?')">
                                            <i class="fas fa-undo me-1"></i> Restore
                                        </a>
                                        <form action="{{ route('product.forceDelete', $data->id) }}" method="post" class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus permanen? Data tidak bisa dikembalikan.')">
                                                <i class="fas fa-trash me-1"></i> Force Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <i class="fas fa-recycle text-secondary mb-3" style="font-size: 48px; opacity: 0.5;"></i>
                                            <h6 class="text-secondary font-weight-normal">Tidak ada data di sampah</h6>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $datas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
