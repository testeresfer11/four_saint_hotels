@extends('admin.layouts.app')

@section('title', 'Add Push Notification')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Add Push Notification</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pushnotification.list') }}">Push Notifications</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.pushnotification.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Body --}}
                    <div class="form-group">
                        <label for="body">Message Body <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="4">{{ old('body') }}</textarea>
                        @error('body') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Image --}}
                    <div class="form-group">
                        <label for="image">Image (optional)</label>
                        <input type="file" name="image" class="form-control-file form-control">
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Type --}}
                    <div class="form-group">
                        <label for="type">Type</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                        @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Notification Type --}}
                    <div class="form-group">
                        <label for="notification_type">Notification Type</label>
                        <input type="text" name="notification_type" class="form-control" value="{{ old('notification_type') }}">
                        @error('notification_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Send To --}}
                    <div class="form-group">
                        <label>Send To <span class="text-danger">*</span></label>
                        <select name="send_to" id="send_to" class="form-control">
                            <option value="">-- Select --</option>
                            <option value="single" {{ old('send_to') == 'single' ? 'selected' : '' }}>Single User</option>
                            <option value="multiple" {{ old('send_to') == 'multiple' ? 'selected' : '' }}>Multiple Users</option>
                            <option value="all" {{ old('send_to') == 'all' ? 'selected' : '' }}>All Users</option>
                        </select>
                        @error('send_to') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Single User --}}
                    <div class="form-group" id="single_user_div" style="display:none;">
                        <label for="receiver_id">Select User</label>
                        <select name="receiver_id" class="form-control">
                            <option value="">-- Select User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('receiver_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Multiple Users --}}
                    <div class="form-group" id="multiple_users_div" style="display:none;">
                        <label for="receiver_ids">Select Multiple Users</label>
                        <select name="receiver_ids[]" class="form-control" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (is_array(old('receiver_ids')) && in_array($user->id, old('receiver_ids'))) ? 'selected' : '' }}>
                                    {{ $user->full_name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('receiver_ids') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-primary">Send Notification</button>
                    <a href="{{ route('admin.pushnotification.list') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('send_to').addEventListener('change', function() {
        document.getElementById('single_user_div').style.display = 'none';
        document.getElementById('multiple_users_div').style.display = 'none';

        if (this.value === 'single') {
            document.getElementById('single_user_div').style.display = 'block';
        } else if (this.value === 'multiple') {
            document.getElementById('multiple_users_div').style.display = 'block';
        }
    });

    // Trigger on load in case old value exists
    document.getElementById('send_to').dispatchEvent(new Event('change'));
</script>
@endsection
