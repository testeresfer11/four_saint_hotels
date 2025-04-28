@extends('admin.layouts.app')
@section('title', 'Add Role')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Roles</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.role.list')}}">Roles</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Role</li>
      </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <form action="{{ route('admin.role.add') }}" method="POST" id="add-role">
        @csrf

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add Role</h4>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>       
                </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @foreach ($permissions as $key => $permission)
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="selection-body">
                                <div class="selection-row selection-row-heading main-heading">
                                    <div class="selection-td">
                                        <input type="checkbox" name="{{$key}}" id="{{$key}}" class="main-td" data-value="{{$key}}">
                                        <label for="{{$key}}">
                                            <h4>{{ucfirst($key)}}</h4>
                                        </label>
                                    </div>
                                </div>
                                <div class="selection-row">
                                    @foreach ($permission as $value)
                                        <div class="selection-td {{$key}}">  
                                            <input class="permission-key" type="checkbox" name="permissions[]" id="{{$value->name}}"  data-value="{{$key}}" value="{{$value->name}}">
                                            @php
                                                $data = explode("-", $value->name);
                                                unset($data[0]);
                                            @endphp
                                            <label for="{{$value->name}}">{{ucfirst(implode(" ",$data))}}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" id="permissions-input" name="assign_permissions" value="[]" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="row">
            <div class="col-12 justify-content-end d-flex">
                <button type="submit" class="btn btn-primary add-role-btn">Add Role</button>
            </div>
        </div>
    </form>
</div>    
@endsection
@section('scripts')
<script>
    $('.main-td').on('click',function(){
        var mainclass = $(this).data('value');
        var isChecked = $(this).prop('checked');
        if (isChecked) {
            $(`.${mainclass}`).find('input[type="checkbox"]').prop('checked',true);
        } else {
            $(`.${mainclass}`).find('input[type="checkbox"]').prop('checked',false);
        }
    });

    $('.permission-key').on('click', function() {
        var mainclass = $(this).data('value'); 

        var allChecked = $(`.${mainclass}`).find('input[type="checkbox"]').length === $(`.${mainclass}`).find('input[type="checkbox"]:checked').length; 
        
        if (allChecked) {
            $(`#${mainclass}`).prop('checked', true);
        } else {
            $(`#${mainclass}`).prop('checked', false);
        }
    });

    $(document).ready(function() {
        $("#add-role").submit(function(e){
            e.preventDefault();
        }).validate({
            rules: {
                name: {
                    required: true,
                    noSpace: true,
                    minlength: 3,
                    maxlength: 25
                },
            },
            messages: {
                name: {
                    required:  "Name is required",
                    minlength: "Name must consist of at least 3 characters",
                    maxlength: "Name must not be greater than 25 characters"
                },
            },
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                if (element.prop('type') === 'file') {
                    error.insertAfter(element.closest('.form-control'));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                var permissions = [];
                
                $("input[name='permissions[]']:checked").each(function() {
                    permissions.push($(this).val());
                });

                $("#permissions-input").val(permissions);

                form.submit();
            }
        });
    });
</script>
@endsection