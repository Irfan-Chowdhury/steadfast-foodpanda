@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('admin_content')
    <h2>📊 Dashboard</h2>
    <p>Welcome to the admin dashboard. You can monitor all activity from here.</p>

    <div class="container m-5">
        <h1 class="text-center">Welcome to Food Panda</h1>
        <h3 class="text-center">Multi-Auth Test Dashboard</h3>
    </div>
@endsection

@push('admin_scripts')

@endpush
