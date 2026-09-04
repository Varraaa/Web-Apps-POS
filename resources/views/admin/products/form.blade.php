@extends('layouts.admin')
@section('title', isset($product) ? 'Edit product — VarStore' : 'New product — VarStore')
@section('content')<div class="admin-wrap narrow-admin">
        <a href="{{ route('admin.products.index') }}" class="back-link">← Back to products</a>
        <div class="admin-head form-head">
            <div>
                <p class="eyebrow">Product studio</p>
                <h1>{{ isset($product) ? 'Edit product' : 'Add new product' }}</h1>
            </div>
        </div>
        <form method="POST"
            action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
            class="product-form">@csrf @isset($product)
                @method('PUT')
                @endisset @foreach ([['name', 'Product name', 'e.g. Essential Overshirt'], ['category', 'Category', 'Outerwear'], ['price', 'Price', '250000'], ['stock', 'Stock', '10'], ['color', 'Color', 'Cloud white'], ['size', 'Sizes', 'S / M / L / XL'], ['image_url', 'Image URL', 'https://images.unsplash.com/...']] as $field)
                    <label>{{ $field[1] }}<input name="{{ $field[0] }}"
                            value="{{ old($field[0], $product->{$field[0]} ?? '') }}" placeholder="{{ $field[2] }}"
                            {{ in_array($field[0], ['name', 'category', 'price', 'stock', 'image_url']) ? 'required' : '' }}></label>
                    @error($field[0])
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                @endforeach
                <label>Description
                    <textarea name="description" required>{{ old('description', $product->description ?? '') }}</textarea>
                </label><label class="check-label"><input type="checkbox" name="is_featured" value="1"
                        @checked(old('is_featured', $product->is_featured ?? false))> Feature this product on the homepage</label><button
                    class="button button-dark">Save product <span>↗</span></button>
            </form>
    </div>@endsection
