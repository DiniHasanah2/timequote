@extends('layouts.app')

@section('content')
<div class="container">
    <!---<h4>VM Mapping</h4>--->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Customer VM Name</th>
                <th>Customer Name</th>
                <th>Project ID</th>
                <th>Quote UUID</th>
                <th>Flavour Mapping</th>
                <th>Service Name Mapping</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vmMappings as $item)
            <tr>
                <td>{{ $item->vm_name }}</td>
                <td>{{ $item->customer_name }}</td>
                <td>{{ $item->project_id }}</td>
                <td>{{ $item->quotation_id }}</td>
                <td>{{ $item->ecs_flavour_mapping }}</td>
                <td>{{ $item->ecs_code }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
