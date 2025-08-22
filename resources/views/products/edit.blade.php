@extends('layouts.app')

@section('content')

        <div class="card shadow-sm">
    <div class="card-header">
        <h5>Edit Product</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Product ID</label>
                <input type="text" class="form-control bg-light" value="{{ $product->id }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Customer</label>
                <input type="text" class="form-control bg-light" value="{{ $product->customer->name ?? '-' }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Quotation ID</label>
                <input type="text" class="form-control bg-light" value="{{ $product->quotation_id }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Service Code</label>
                <select name="services_id" class="form-select" required>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ $service->id == $product->services_id ? 'selected' : '' }}>
                            {{ $service->code }} - {{ $service->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" value="{{ $product->quantity }}" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Price Per Unit</label>
                <input type="number" name="priceperunit" class="form-control" value="{{ $product->priceperunit }}" min="0" step="any" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Total Price</label>
                <input type="number" name="totalprice" class="form-control" value="{{ $product->totalprice }}" min="0" step="any" required>
            </div>

            <button type="submit" class="btn btn-pink">Update Product</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
