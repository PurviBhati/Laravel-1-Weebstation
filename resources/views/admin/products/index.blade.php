@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="card">
        <div class="header">
            <h1>Products</h1>
            <a href="/admin/products/create" class="btn">Add Product</a>
        </div>

        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>For Whom</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th></th>
                <th></th>
            </tr>

            @forelse ($products as $product)
                <tr>
                    @php
                        $image = $product->images->first();
                    @endphp

                    <td>
                        @if ($image)
                            <img
                                class="thumb"
                                src="{{ asset('storage/' . $image->path) }}"
                                alt="{{ $product->name }}"
                            >
                        @else
                            <div class="thumb"></div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if ($product->is_featured) <span title="Featured">★</span> @endif
                    </td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ ucfirst($product->for_whom) }}</td>
                    <td>₹{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <span class="badge badge-{{ $product->status }}">{{ ucfirst($product->status) }}</span>
                    </td>
                    <td>
                        <a href="/admin/products/{{ $product->id }}/edit" class="btn" style="background:green;">Edit</a>
                    </td>
                    <td>
                        <form method="POST" action="/admin/products/{{ $product->id }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none; border:0; color:#dc2626; cursor:pointer; font-size:16px" onclick="return confirm('Delete this product?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No products yet.</td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection