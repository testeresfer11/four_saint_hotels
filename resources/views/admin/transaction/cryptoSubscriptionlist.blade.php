@extends('admin.layouts.app')
@section('title', 'Crypto Subscription')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Crypto Subscription</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Crypto Subscription</li>
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
            <h4 class="card-title">Crypto Subscription</h4>
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
                  <th> User Name</th>
                  <th> Amount</th>
                  <th> Payment Method</th>
                  <th> Payment Status</th>
                  <th> Transaction Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($transactions as $key => $data)
               
                  <tr>
                    <td> {{ ++$key }} </td>
                    <td> {{UserNameById($data->user_id)}} </td>
                    <td> {{$data->amount}} </td>
                    <td> {{ucfirst($data->method)}} </td>
                    <td>
                      @if (($data->status == 'Paid') || $data->status == 'COMPLETED')
                          <span class="badge badge-success">Paid</span>
                      @elseif ($data->status == 'Pending')
                          <span class="badge badge-warning">Pending</span>
                      @elseif ($data->status == 'Expired')
                          <span class="badge badge-danger">Expired</span>
                      @elseif ($data->status == 'Failed')
                          <span class="badge badge-secondary">Failed</span>
                      @else
                          <span class="badge badge-light">Unknown</span>
                      @endif
                    </td>
                    
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

