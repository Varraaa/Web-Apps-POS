@extends('layouts.admin')
@section('title', 'Product studio — VarStore')
@section('content')<div class="admin-wrap">
        <div class="admin-head">
            <div>
                <p class="eyebrow">Admin studio</p>
                <h1>Product catalogue</h1>
                <p>Manage the pieces your customers see.</p>
            </div><a href="{{ route('admin.products.create') }}" class="button button-dark">New product <span>+</span></a>
        </div>
        @if (session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif
        <div class="admin-tabs">
            <a class="tab-active" href="{{ route('admin.products.index') }}">Products</a><a
                href="{{ route('admin.orders.index') }}">Orders</a>
        </div>
        <div class="admin-table">
            <div class="admin-table-head">
                <span>Product</span><span>Category</span><span>Price</span><span>Stock</span><span>Featured</span><span></span>
            </div>
            @foreach ($products as $product)
                <div class="admin-row">
                    <div class="admin-product"><img src="{{ $product->image_url }}" alt="">
                        <div><b>{{ $product->name }}</b><small>{{ $product->color }}</small></div>
                    </div><span>{{ $product->category }}</span><span>Rp
                        {{ number_format($product->price, 0, ',', '.') }}</span><span>{{ $product->stock }}</span><span>{{ $product->is_featured ? 'Yes' : '—' }}</span>
                    <div class="row-actions"><a href="{{ route('admin.products.edit', $product) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}">@csrf
                            @method('DELETE')<button>Delete</button></form>
                    </div>
                </div>
            @endforeach
        </div>{{ $products->links() }}
</div>@endsection
