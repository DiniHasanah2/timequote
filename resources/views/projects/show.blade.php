@extends('layouts.app') 

@section('content')
<div class="container">
    <h2>Project Details</h2>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Project ID</th>
                <td>{{ $project->id }}</td>
            </tr>
            <tr>
                <th>Project Name</th>
                <td>{{ $project->name }}</td>
            </tr>
            <tr>
                <th>Customer Name</th>
                <td>{{ $customer->name }}</td>
            </tr>
            <tr>
                <th>Industry</th>
                <td>{{ $customer->industry ?? '-' }}</td>
            </tr>
            <tr>
                <th>Presale Name</th>
                <td>{{ $customer->presale_name }}</td>
            </tr>
            <tr>
                <th>Date Created</th>
                <td>{{ $project->formatted_created_at }}</td> <!-- changing format -->
            </tr>
        </tbody>
    </table>
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('presale.dashboard') }}" 
        class="btn btn-pink">
        Back to Dashboard
     </a>
     
</div>
@endsection
    
