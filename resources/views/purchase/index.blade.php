@extends('be.master')

@section('menu')
    @include('be.menu')
@endsection

@section('purchase')
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $title }}</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">{{ $title }}</h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center"></div>
                <ul class="navbar-nav  justify-content-end">
                    <li class="nav-item d-flex align-items-center">
                        <div class="mx-3">
                            <a href="{{ route('purchase.create') }}" class="btn bg-gradient-primary btn-sm mb-0">Add New {{ $title }}</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>{{ $title }} Data</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No Nota</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Distributor</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Bayar</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $nmr => $data)
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 ps-3">{{ $datas->firstItem() + $nmr }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $data->no_nota }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $data->tgl_nota }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $data->distributor->nama_distributor ?? '-' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">Rp {{ number_format($data->total_bayar) }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-link text-primary px-2 mb-0" data-bs-toggle="modal" data-bs-target="#detailModal{{ $data->id }}" title="View Details">
                                                    <i class="fas fa-eye text-primary" style="font-size: 18px;"></i>
                                                </button>
                                                <a href="{{ route('purchase.edit', $data->id) }}" class="btn btn-link text-info px-2 mb-0" data-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-pencil-alt text-info" style="font-size: 18px;"></i>
                                                </a>
                                                <form action="{{ route('purchase.destroy', $data->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger px-2 mb-0" data-toggle="tooltip" title="Hapus">
                                                        <i class="fas fa-trash text-danger" style="font-size: 18px;"></i>
                                                    </button>
                                                </form>

                                                <!-- Detail Modal -->
                                                <div class="modal fade" id="detailModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel{{ $data->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="detailModalLabel{{ $data->id }}">Detail Pembelian: {{ $data->no_nota }}</h5>
                                                                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <div class="row mb-3">
                                                                    <div class="col-4">
                                                                        <p class="text-xs text-secondary mb-0">Tanggal:</p>
                                                                        <p class="text-sm font-weight-bold">{{ $data->tgl_nota }}</p>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <p class="text-xs text-secondary mb-0">Distributor:</p>
                                                                        <p class="text-sm font-weight-bold">{{ $data->distributor->nama_distributor ?? '-' }}</p>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <p class="text-xs text-secondary mb-0">Total Bayar:</p>
                                                                        <p class="text-sm font-weight-bold">Rp {{ number_format($data->total_bayar) }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <table class="table align-items-center mb-0">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Item</th>
                                                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Cost</th>
                                                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Qty</th>
                                                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-end">Subtotal</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($data->details as $detail)
                                                                            <tr>
                                                                                <td><p class="text-xs font-weight-bold mb-0 text-start">{{ $detail->product->nama_barang ?? 'Unknown' }}</p></td>
                                                                                <td class="text-center"><p class="text-xs font-weight-bold mb-0">Rp {{ number_format($detail->harga_beli) }}</p></td>
                                                                                <td class="text-center"><p class="text-xs font-weight-bold mb-0">{{ $detail->jumlah_beli }}</p></td>
                                                                                <td class="text-end"><p class="text-xs font-weight-bold mb-0">Rp {{ number_format($detail->subtotal) }}</p></td>
                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-3 border-top">
                            {{ $datas->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

