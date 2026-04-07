@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert" style="margin-bottom: 20px; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 4px;">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="float: right; font-size: 21px; font-weight: 700; line-height: 1; color: #000; opacity: .5; background: transparent; border: 0; cursor: pointer;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                <h5>Footer Settings</h5>
            </div>
            <div class="card-block">
                <form action="{{ route('admin.footer.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ $footer->company_name ?? '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Company Description</label>
                        <textarea name="company_description" class="form-control" rows="3" required>{{ $footer->company_description ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $footer->contact_email ?? '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ $footer->contact_phone ?? '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Contact Address</label>
                        <input type="text" name="contact_address" class="form-control" value="{{ $footer->contact_address ?? '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Facebook URL</label>
                        <input type="text" name="social_facebook" class="form-control" value="{{ $footer->social_facebook ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Twitter URL</label>
                        <input type="text" name="social_twitter" class="form-control" value="{{ $footer->social_twitter ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Instagram URL</label>
                        <input type="text" name="social_instagram" class="form-control" value="{{ $footer->social_instagram ?? '' }}">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Footer Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto hide alert after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endsection