@extends('be.master')

@section('menu')
    @include('be.menu')
@endsection

@section('user')
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
                            <a href="{{ route('user.create') }}" class="btn bg-gradient-primary btn-sm mb-0">Add New {{ $title }}</a>
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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Foto</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Phone</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $nmr => $data)
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 ps-3">{{ $nmr + 1 }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="cursor-pointer" data-bs-toggle="modal" data-bs-target="#userModal{{ $data->id }}">
                                                        <img src="{{ $data->foto && $data->foto != 'default.png' ? asset('images/users/' . $data->foto) : asset('assets/img/team-2.jpg') }}" class="avatar avatar-sm me-3 border-radius-lg shadow" alt="user1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $data->name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $data->email }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ ucfirst($data->role) }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $data->no_telepon }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('user.edit', $data->id) }}" class="btn btn-link text-info px-2 mb-0" data-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-pencil-alt text-info" style="font-size: 18px;"></i>
                                                </a>
                                                <form action="{{ route('user.destroy', $data->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger px-2 mb-0" data-toggle="tooltip" title="Hapus">
                                                        <i class="fas fa-trash text-danger" style="font-size: 18px;"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- User Detail Modal (KTP Style) -->
                                        <div class="modal fade" id="userModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="userModalLabel{{ $data->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content border-radius-xl shadow-lg border-0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title font-weight-bolder text-info text-gradient" id="userModalLabel{{ $data->id }}">KARTU TANDA PENGGUNA - MADURA MART</h5>
                                                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body pt-0">
                                                        <hr class="horizontal dark mt-0 mb-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-8">
                                                                <div class="p-3" style="font-family: 'Courier New', Courier, monospace; background: rgba(255, 255, 255, 0.4); border-radius: 15px; border: 1px solid rgba(255,255,255,0.6);">
                                                                    <div class="row mb-2">
                                                                        <div class="col-4 text-xs font-weight-bold text-uppercase opacity-7">User ID</div>
                                                                        <div class="col-8 text-sm font-weight-bolder">: {{ str_pad($data->id, 8, '0', STR_PAD_LEFT) }}</div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <div class="col-4 text-xs font-weight-bold text-uppercase opacity-7">Nama</div>
                                                                        <div class="col-8 text-sm font-weight-bolder">: {{ strtoupper($data->name) }}</div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <div class="col-4 text-xs font-weight-bold text-uppercase opacity-7">Email</div>
                                                                        <div class="col-8 text-sm font-weight-bolder">: {{ $data->email }}</div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <div class="col-4 text-xs font-weight-bold text-uppercase opacity-7">Alamat</div>
                                                                        <div class="col-8 text-sm font-weight-bolder">: {{ $data->alamat ?? 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <div class="col-4 text-xs font-weight-bold text-uppercase opacity-7">Telepon</div>
                                                                        <div class="col-8 text-sm font-weight-bolder">: {{ $data->no_telepon ?? 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <div class="col-4 text-xs font-weight-bold text-uppercase opacity-7">Role</div>
                                                                        <div class="col-8 text-sm font-weight-bolder text-info text-gradient">: {{ strtoupper($data->role) }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 text-center mt-3 mt-md-0">
                                                                <div class="position-relative d-inline-block">
                                                                    <img src="{{ $data->foto && $data->foto != 'default.png' ? asset('images/users/' . $data->foto) : asset('assets/img/team-2.jpg') }}" 
                                                                         class="img-fluid border-radius-lg shadow-lg" 
                                                                         style="width: 160px; height: 200px; object-fit: cover; border: 4px solid white;">
                                                                    <div class="bg-white border-radius-sm p-1 shadow-sm mt-n3 mx-auto" style="width: fit-content; position: relative; z-index: 2;">
                                                                        <small class="font-weight-bold text-xxs px-2 text-info">OFFICIAL MEMBER</small>
                                                                    </div>
                                                                </div>
                                                                <p class="mt-3 text-xs font-weight-bold opacity-6">BERLAKU SELAMANYA</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if (session('simpan'))
            swal("Success", "{{ session('simpan') }}", "success");
        @endif
        @if (session('ubah'))
            swal("Success", "{{ session('ubah') }}", "success");
        @endif
        @if (session('hapus'))
            swal("Deleted", "{{ session('hapus') }}", "success");
        @endif
    </script>
@endsection
