{{-- Extends layout --}}
@extends('admin.layout.default')

{{-- Content --}}
@section('content')
    @if (Session::has('message'))
        <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif
    <div class="card card-custom">
        <div class="card-body">
            <h3>Personal Information</h3><hr>
            <div>
                <p><span><strong>First Name:</strong></span> {{$vendorusers['first_name']}}</p>
                <p><span><strong>Last Name:</strong></span> {{$vendorusers['last_name']}}</p>
                <p><span><strong>Email:</strong></span> {{$vendorusers['email']}}</p>
                <p><span><strong>Phone Number:</strong></span> {{$vendorusers['phone']}}</p>
                <p><span><strong>Address:</strong></span> {{$vendorusers['address']}}</p>
                <p><span><strong>Assign Sales-Person:</strong></span> {{$username['first_name']}}</p>
                <p><span><strong>Profile Picture:</strong></span> <img src="{{ URL::to('uploads/profile/').'/'.$vendorusers['profilepic'] }}" width="50" height="50" /></p>
            </div> 
            <hr><h3>Business Information</h3><hr>
            <div>
                <p><span><strong>Enterprise Name:</strong></span> {{$vendorusers['enterprise_name']}}</p>
                <p><span><strong>Industry:</strong></span> {{$vendorusers['industry']}}</p>
                <p><span><strong>Business Experience(in yrs):</strong></span> {{$vendorusers['business_exp']}}</p>
                <p><span><strong>Sales (per/month):</strong></span> {{$vendorusers['sales']}}</p>
                <p><span><strong>Turn Over (Yearly):</strong></span> {{$vendorusers['turn_over']}}</p>
                <p><span><strong>Mode of Payment:</strong></span> {{$vendorusers['payment_mode']}}</p>
                <p><span><strong>Is Downloadable:</strong></span> {{$vendorusers['downloadable']}}</p>
                <p><span><strong>Payment Interval:</strong></span> {{$vendorusers['payment_interval']}}</p>
                <p><span><strong>Business Logo:</strong></span> <img src="{{ URL::to('uploads/business_logo/').'/'.$vendorusers['business_logo'] }}" width="50" height="50" /></p>
            </div>        
        </div>
    </div>
@endsection

{{-- Styles Section --}}

{{-- Scripts Section --}}
@section('scripts')
    {{-- page scripts --}}
    <!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script> -->
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection
