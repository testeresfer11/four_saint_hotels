@extends('admin.layouts.app')
@section('title', 'Booking Transactions')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Booking Transactions</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Transactions</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center py-3 px-3">
                    <h4 class="card-title m-0">Booking Transactions</h4>
                    <form id="filter" method="GET">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-4">
                                <input type="text" name="search_keyword" class="form-control" placeholder="Search Booking Code" value="{{ request('search_keyword') }}">
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="px-2">From</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="px-2">To</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                @if(request()->filled('search_keyword') || request()->filled('from_date') || request()->filled('to_date'))
                                    <a href="{{ route('admin.payment.list') }}" class="btn btn-danger">Clear</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-stripe">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Booking Code</th>
                                <th>Amount</th>
                                <th>Currency</th>
                                <th>Payment Method</th>
                                <th>Payment Date</th>
                                <th>Status</th>
                                <th>Room</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $index => $payment)
                                <tr>
                                    <td>{{ $payments->firstItem() + $index }}</td>
                                    <td>{{ $payment['booking']['reservation_code'] ?? 'N/A' }}</td>
                                    <td>{{ $payment['amount'] }}</td>
                                    <td>{{ $payment['currency'] }}</td>
                                    <td>{{ ucfirst($payment['payment_type']) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment['payment_date'])->format('d M Y, H:i') }}</td>
                                    <td>{{ $payment['payment_status'] }}</td>
                                    <td>{{ $payment['booking']['room_name'] ?? 'N/A' }}</td>
                                    <td> 
                                    <span class="menu-icon">
                                            <a href="{{route('admin.booking.view',['id' => $payment['booking']['id']])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                                          </span>&nbsp;&nbsp;&nbsp;
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($payments, 'links'))
                    <div class="custom_pagination">
                        {{ $payments->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection


