@extends('admin.layouts.master')
@section('title', 'Content Management')
@section('content')

<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-2">
        <h2 class="f-20 bold title-main">Content Management</h2>
    </div>
    <div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-light  shadow">
                <thead class="bg-e6 rounded-8">
                    <tr>
                        <th>Title</th>
                        <th></th>
                        <th>Slug</th>
                        <th></th>
                        <th></th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($pages->count()>0)
                        @foreach($pages as $pages)
                            <tr>
                                <td>
                                    {{$pages->PageContent->name}}
                                </td>
                                <td></td>
                                <td>
                                {{$pages->slug}}
                                </td>
                                <td></td>
                                <td></td>

                                <td>
                                    <div class="td-delete-icon d-flex gap-3">
                                        <a href="{{route('edit-page-content',['slug'=>$pages->slug])}}">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="7" class="text-center">No Page found</td>
                    </tr>
                    @endif
                
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/k6vov7xs3x5yy8qq6m6nl4qolwen4gg1kedvjbqk7cae33hv/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection