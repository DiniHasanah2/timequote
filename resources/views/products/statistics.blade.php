@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Product Statistics by Category</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Category</th>
                    <th>Most Quoted Item</th>
                    <th>Total Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats as $row)
                <tr>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['most_quoted_item'] }}</td>
                    <td>{{ $row['total_quantity'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
