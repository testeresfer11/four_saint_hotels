@extends('admin.layouts.app')
@section('title', 'Transaction')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Transactions</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Transactions</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Transactions</h4>
              <div class="admin-filters">
                <form id="filter">
                  <div class="row align-items-end justify-content-end mb-3">
                    <div class="col-3 d-flex gap-2">
                        <input type="text" class="form-control"  placeholder="Search" name="search_keyword" value="{{request()->filled('search_keyword') ? request()->search_keyword : ''}}" >            
                    </div>
                      <div class="col-3 gap-2">
                        <label for="From">From</label>
                          <input type="date" class="form-control dateToInput"  placeholder="Search" name="from_date" value="{{request()->filled('from_date') ? request()->from_date : ''}}" >            
                      </div>
                      <div class="col-3 gap-2">
                        <label for="To">To</label>
                          <input type="date" class="form-control dateToInput"  placeholder="Search" name="to_date" value="{{request()->filled('to_date') ? request()->to_date : ''}}" >            
                      </div>
                     
                      <div class="col-3">
                        <div class="filter-btns d-flex gap-2 align-items-center">
                          <button type="submit" class="btn btn-primary">Filter</button>
                          @if(request()->filled('search_keyword') || request()->filled('from_date') || request()->filled('to_date'))
                              <button class="btn btn-danger" id="clear_filter">Clear Filter</button>
                          @endif
                        </div>
                      </div>
                  </div>
                </form>
              </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Sr. No. </th>
                  <th> Transaction Id</th>
                  <th> User Name</th>
                  <th> Transaction Amount</th>
                  <th> Payment Method</th>
                  <th> Order Id</th>
                  <th> Transaction Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($transactions as $key => $data)
               
                  <tr>
                    <td> {{ ++$key }} </td>
                    <td> {{ ($data->payment_id != '') ? $data->payment_id : 'N/A'}} </td>
                    <td> {{UserNameById($data->user_id)}} </td>
                    <td> {{$data->amount}} </td>
                    <td> {{$data->payment_type ?  ucfirst($data->payment_type) : 'Paypal'}} </td>
                    <td> {{$data->order ? $data->order->uuid : 'N/A'}} </td>
                    <td> {{ convertDate($data->created_at,'d M,Y') }} </td>
                  </tr>
                @empty
                    <tr>
                      <td colspan="8" class="no-record"> <center>No record found </center></td>
                    </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="custom_pagination">
            {{ $transactions->appends(request()->query())->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

