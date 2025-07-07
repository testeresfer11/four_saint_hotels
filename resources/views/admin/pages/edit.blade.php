@extends('admin.layouts.master')
@section('title', 'Content Management')
@section('content')

<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-2">
        <h2 class="f-20 bold title-main">Content Management</h2>
    </div>
    <div class="card">
        <form action="{{ route('pages.update') }}" method="POST">
            @csrf
            <input type="hidden" name="page_id" id="page_id" value="{{$page->id}}">
            <div class="content-page-form shadow rounded-20 p-lg-3 p-3">
                <div class="col-md-12">
                    <div class="c-title-input mb-3">
                        <label for="content_title" class="form-label fw-600">Title<span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-30 bg-f6 @error('content_title') is-invalid @enderror" id="content_title" name="content_title" placeholder="title" value="{{$page->PageContent->name}}">
                    </div>
                    @error('content_title')
                        <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <div class="c-title-input">
                        <label for="page_slug" class="form-label fw-600">Slug</label>
                        <input type="text" class="form-control rounded-30 bg-f6 @error('page_slug') is-invalid @enderror" id="page_slug" name="page_slug" value="{{$page->slug}}" disabled>
                    </div>
                </div>
                @error('page_slug')
                    <div class="invalid-feedback">{{$message}}</div>
                @enderror
                <div class="text-editor-box">
                    <h6 class="color-23 fw-600 mt-3">Product Information</h6>
                    <div id="wysiwyg">
                        <textarea id="content_editor" name="content" class="form-control rounded-30 bg-f6">
                        {!! $page->PageContent->page_content !!}
                        </textarea>
                    </div>
                    <div class="submit-btn mt-4">
                        <button type="submit" class="btn btn-primary">Update</button>
                        {{-- <button type="submit" class="bg-black text-white px-5 py-2 rounded-8 fw-500">Update</button> --}}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/k6vov7xs3x5yy8qq6m6nl4qolwen4gg1kedvjbqk7cae33hv/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
  });
</script>



<script>
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection