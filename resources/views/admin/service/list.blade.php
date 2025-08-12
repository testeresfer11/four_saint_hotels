@extends('admin.layouts.app')

@section('title', 'Service List')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Services</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Service List</li>
            </ol>
        </nav>
        <a href="{{ url('/api/sabee/service/fetch') }}">
            <button type="button" class="btn btn-primary btn-md">
                <span class="menu-icon"><i class="fa-solid fa-plus"></i></span>
                <span class="menu-text">Get Services</span>
            </button>
        </a>

    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <div class="px-3 py-3">
                <h4 class="card-title m-0">Services List</h4>
            </div>
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

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr>
                                <td>{{ $service->service_name }}</td>
                                <td>{{ $service->service_category_name }}</td>
                                <td>{{ Str::limit($service->description, 50, '...') }}</td>
                                <td>{{ $service->currency }} {{ number_format($service->price, 2) }}</td>
                                <td>
                                    <a href="{{ route('service.view', $service->id) }}" class="btn btn-info btn-sm">View</a>
                                    <a href="{{ route('service.edit', $service->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No services available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $services->links() }}
            </div>
        </div>
    </div>
@endsection
