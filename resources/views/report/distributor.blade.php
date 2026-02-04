@extends('be.master')
@section('menu')@include('be.menu')@endsection
@section('report')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between">
          <h6>{{$title}}</h6>
          <button onclick="window.print()" class="btn btn-primary btn-sm">Print Report</button>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Distributor Name</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Address</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Phone</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($datas as $nmr => $data)
                <tr>
                  <td class="ps-4"><p class="text-xs font-weight-bold mb-0">{{$nmr + 1}}</p></td>
                  <td><p class="text-xs font-weight-bold mb-0">{{$data->nama_distributor}}</p></td>
                  <td><p class="text-xs font-weight-bold mb-0">{{$data->alamat_distributor}}</p></td>
                  <td><p class="text-xs font-weight-bold mb-0">{{$data->notelepon_distributor}}</p></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
