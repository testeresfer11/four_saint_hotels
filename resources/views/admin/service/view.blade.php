@extends('admin.layouts.app')

@section('title', 'View Service')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Services</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('service.list') }}">Service List</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Service</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">View Service</h4>

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <h5>Service Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Name</th>
                            <td>{{ $service->service_name }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>{{ $service->service_category_name }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $service->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>{{ $service->currency }} {{ number_format($service->price, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Price Type</th>
                            <td>{{ ucfirst($service->price_type) }}</td>
                        </tr>
                        <tr>
                            <th>VAT</th>
                            <td>{{ $service->vat }}%</td>
                        </tr>
                        <tr>
                            <th>Currency</th>
                            <td>{{ $service->currency }}</td>
                        </tr>
                        <tr>
                            <th>City Tax Applied</th>
                            <td>{{ $service->apply_city_tax ? 'Yes' : 'No' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Service Image</h5>
                    <div class="text-center">
                        <img src="{{ $service->image_url }}" alt="Service Image" class="img-fluid" style="max-height: 250px;">
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Available Rate Plans</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Rate Plan Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($service->available_rateplans as $rateplan)
                                <tr>
                                    <td>{{ $rateplan['rateplan_name'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12 text-right">
                    <a href="{{ route('service.list') }}" class="btn btn-secondary">Back to Service List</a>
                    <a href="{{ route('service.edit', $service->id) }}" class="btn btn-warning">Edit Service</a>
                </div>
            </div>
        </div>
    </div>
@endsection
