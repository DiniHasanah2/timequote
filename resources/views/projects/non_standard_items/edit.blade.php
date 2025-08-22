@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5>Edit Non-Standard Item</h5>
    </div>
    <div class="card-body">

    <div class="card-body">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

        <form method="POST" action="{{ route('non_standard_items.update', [$version->id, $item->id]) }}">
            @csrf
            @method('PUT')

            <!-- Input fields -->
            <div class="mb-3">
                <label>Item Name</label>
                <input type="text" name="item_name" class="form-control" value="{{ $item->item_name }}">
            </div>
         
            
            <div class="mb-3">
    <label>Unit</label>
    <select name="unit" class="form-select">
        @foreach (['Unit', 'GB', 'Mbps', 'Pair', 'Domain', 'VM', 'Hours', 'Days'] as $opt)
            <option value="{{ $opt }}" @if($item->unit === $opt) selected @endif>{{ $opt }}</option>
        @endforeach
    </select>
</div>

         
                <!---   <option value="{{ $item->unit }}">{{ $opt }}</option> --->
            
   

    <div class="mb-3"> 
    <label>Quantity</label>
    <input type="number" name="quantity" class="form-control" value="{{ $item->quantity }}" min="0">
</div>

    <div class="mb-3"> <label>Cost</label>
                <input type="number" name="cost" class="form-control" value="{{ $item->cost }}" min="0"></div>
    <div class="mb-3">   <label>Mark Up (%)</label>
                <input type="number" name="mark_up" class="form-control" value="{{ $item->mark_up }}" min="0">
            </div>
    <div class="mb-3">
          <label>Selling Price (RM)</label>
                <input type="0.01" name="selling_price" class="form-control" value="{{ $item->selling_price }}" readonly style="background-color: black; color: white;" min="0">
    
     </div>

            <button type="submit" class="btn btn-pink">Update</button>
          
            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="btn btn-secondary">Back</a>

        </form>
    </div>
</div>




<script>
document.addEventListener('DOMContentLoaded', function() {
    const costInput = document.querySelector('input[name="cost"]');
    const markUpInput = document.querySelector('input[name="mark_up"]');
    const sellingPriceInput = document.querySelector('input[name="selling_price"]');

    function calculateSellingPrice() {
        const cost = parseFloat(costInput.value) || 0;
        const markup = parseFloat(markUpInput.value) || 0;
        const sellingPrice = cost + (cost * (markup / 100));
        sellingPriceInput.value = sellingPrice.toFixed(2); // 2 decimal places
    }

    costInput.addEventListener('input', calculateSellingPrice);
    markUpInput.addEventListener('input', calculateSellingPrice);
});
</script>
@endsection
