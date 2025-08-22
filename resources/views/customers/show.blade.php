@extends('layouts.app') 

@section('content')
<div class="container">
    <h2>Customer Details</h2>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Customer ID</th>
                <td>{{ $customer->id }}</td>
            </tr>
            <tr>
                <th>Customer Name</th>
                <td>{{ $customer->name }}</td>
            </tr>
            <tr>
                <th>Business Number</th>
                <td>{{ $customer->business_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $customer->department ?? '-' }}</td>
            </tr>
            <tr>
                <th>Client Manager</th>
                <td>{{ $customer->client_manager }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $customer->presale_name }}</td>
            </tr>
            <tr>
                <th>Date Created</th>
                <td>{{ $customer->formatted_created_at }}</td> <!-- Format yang sudah diubah -->
            </tr>
        </tbody>
    </table>
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('presale.dashboard') }}" 
        class="btn btn-pink">
        Back to Dashboard
     </a>
     
</div>
@endsection
    
