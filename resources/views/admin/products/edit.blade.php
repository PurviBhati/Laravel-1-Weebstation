@extends('layouts.app')

@section('title', 'Edit Product | Admin')

@section('content')

<style>
    .product-form {
        max-width: 850px;
        margin: 0 auto;
    }

    .product-form h1 {
        margin-bottom: 5px;
    }

    .form-subtitle {
        color: #6b7280;
        margin-bottom: 25px;
    }

    .form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
    }

    .field {
        margin-bottom: 18px;
    }

    .field:last-child {
        margin-bottom: 0;
    }

    .field > label,
    .section-label {
        display: block;
        margin-bottom: 7px;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
    }

    .input,
    .select,
    .textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #fff;
    }

    .input:focus,
    .select:focus,
    .textarea:focus {
        outline: none;
        border-color: #111827;
    }

    .select:disabled {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }

    .input[readonly] {
        background: #f3f4f6;
    }

    .textarea {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }

    .two-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .category-header label {
        margin: 0;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
    }

    .category-header .btn {
        padding: 5px 10px;
        font-size: 13px;
    }

    /* Sizes and stock */
    .sizes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .sizes-title {
        font-weight: 600;
        font-size: 14px;
        color: #374151;
    }

    .sizes-help {
        margin: 0 0 12px;
        font-size: 13px;
        color: #6b7280;
    }

    .size-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        gap: 12px;
        padding: 12px;
        margin-bottom: 8px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
    }

    .size-row label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-weight: 400;
        cursor: pointer;
    }

    .field-error {
        margin-top: 5px;
        font-size: 13px;
        color: #b91c1c;
    }

    .status-options {
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
    }

    .status-options label {
        margin: 0;
        font-weight: 400;
        cursor: pointer;
    }

    .featured {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .featured label {
        margin: 0;
        font-weight: 400;
        cursor: pointer;
    }

    .upload-box {
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        background: #fafafa;
    }

    .upload-box p {
        margin: 0 0 10px;
        color: #6b7280;
        font-size: 13px;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
        margin-top: 15px;
    }

    .preview-item {
        aspect-ratio: 1;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        background: #f3f4f6;
    }

    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .error-box {
        margin-bottom: 20px;
        padding: 12px 15px;
        border-radius: 8px;
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        font-size: 14px;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid #d1d5db;
    }

    .btn-light {
        background: #fff;
        color: #374151;
    }

    .btn-primary {
        background: #111827;
        color: #fff;
        border-color: #111827;
    }

    /* Current images */
    .current-images {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin: 12px 0 22px;
    }

    .image-item {
        position: relative;
        width: 110px;
        height: 110px;
        flex: 0 0 110px;
        box-sizing: border-box;
        border-radius: 8px;
    }

    .image-item img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        box-sizing: border-box;
        transition: opacity 0.2s ease;
    }

    .remove-image-checkbox {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    .remove-image-button {
        appearance: none;
        -webkit-appearance: none;

        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 5;

        width: 28px;
        height: 28px;
        min-width: 28px;
        min-height: 28px;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 0;
        margin: 0;

        border: 2px solid #fff;
        border-radius: 50%;

        background: #ef4444;
        color: #fff;

        font-family: Arial, sans-serif;
        font-size: 22px;
        font-weight: 700;
        line-height: 1;

        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    }

    .remove-image-button:hover {
        background: #dc2626;
    }

    .image-item.marked-for-removal img {
        opacity: 0.3;
        border: 2px solid #ef4444;
    }

    .image-item.marked-for-removal .remove-image-button {
        background: #16a34a;
    }

    .image-item.marked-for-removal .remove-image-button:hover {
        background: #15803d;
    }

    @media (max-width: 650px) {
        .two-columns {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .preview-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .form-card {
            padding: 18px;
        }
    }
</style>

@php
    $currentAudienceId = old('audience_id', $product->category?->audience_id);
    $currentCategoryId = old('category_id', $product->category_id);
@endphp

<div class="product-form">

    <h1>Edit Product</h1>

    <p class="form-subtitle">
        Update product details, category, sizes, status, and images.
    </p>

    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form
        id="productForm"
        action="{{ url('/admin/products/' . $product->id) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="form-card">

            {{-- Product Name --}}
            <div class="field">
                <label for="name">Product Name</label>

                <input
                    class="input"
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $product->name) }}"
                    placeholder="Enter product name"
                    required
                >
            </div>

            {{-- Audience and Category --}}
            <div class="two-columns">

                <div class="field">
                    <label for="audience_id">Audience</label>

                    <select class="select" name="audience_id" id="audience_id" required>
                        <option value="">Select Audience</option>

                        @foreach ($audiences as $audience)
                            <option
                                value="{{ $audience->id }}"
                                @selected($currentAudienceId == $audience->id)
                            >
                                {{ $audience->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <div class="category-header">
                        <label for="category_id">Category</label>

                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light">
                            + Add Category
                        </a>
                    </div>

                    <select class="select" name="category_id" id="category_id" required>
                        <option value="">Select Category</option>

                        @foreach ($categories as $category)
                            @if ($category->audience_id == $currentAudienceId)
                                <option
                                    value="{{ $category->id }}"
                                    @selected($currentCategoryId == $category->id)
                                >
                                    {{ $category->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Sizes and Individual Stock --}}
            <div class="field" id="sizes-section" style="display:none;">

                <div class="sizes-header">
                    <span class="sizes-title">Available Sizes &amp; Stock</span>

                    <button type="button" id="clear-sizes" class="btn btn-light">
                        Clear Sizes
                    </button>
                </div>

                <p class="sizes-help" id="sizes-help">
                    Select the sizes you want to sell and enter stock for each selected size.
                    Minimum stock per selected size is 1.
                </p>

                <div id="sizes-container"></div>

                <div id="sizes-error" class="field-error">
                    @error('variants')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            {{-- Price and Total Stock --}}
            <div class="two-columns">

                <div class="field">
                    <label for="price">Price (₹)</label>

                    <input
                        class="input"
                        type="number"
                        name="price"
                        id="price"
                        min="0"
                        step="0.01"
                        value="{{ old('price', $product->price) }}"
                        placeholder="₹ 0.00"
                        required
                    >
                </div>

                <div class="field">
                    <label for="stock">Total Stock</label>

                    <input
                        class="input"
                        type="number"
                        name="stock"
                        id="stock"
                        min="0"
                        step="1"
                        value="{{ old('stock', $product->stock) }}"
                        readonly
                    >

                    @error('stock')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Description --}}
            <div class="field">
                <label for="description">Description</label>

                <textarea
                    class="textarea"
                    name="description"
                    id="description"
                    placeholder="Enter product description..."
                >{{ old('description', $product->description) }}</textarea>
            </div>

            {{-- Status --}}
            <div class="field">
                <label>Status</label>

                <div class="status-options">
                    <label>
                        <input
                            type="radio"
                            name="status"
                            value="draft"
                            @checked(old('status', $product->status) == 'draft')
                            required
                        >
                        Draft
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="status"
                            value="active"
                            @checked(old('status', $product->status) == 'active')
                        >
                        Active
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="status"
                            value="archived"
                            @checked(old('status', $product->status) == 'archived')
                        >
                        Archived
                    </label>
                </div>
            </div>

            {{-- Featured --}}
           <!-- <div class="field">
                <div class="featured">
                    <input
                        type="checkbox"
                        name="is_featured"
                        id="is_featured"
                        value="1"
                        @checked(old('is_featured', $product->is_featured))
                    >

                    <label for="is_featured">Show on the home page (featured)</label>
                </div>
            </div>  -->

            {{-- Current Images --}}
            <h4 class="section-label">Current Product Images</h4>

            <div class="current-images">
                @forelse ($product->images as $image)
                    <div class="image-item" id="image-item-{{ $image->id }}">
                        <img
                            src="{{ asset('storage/' . $image->path) }}"
                            alt="{{ $product->name }}"
                        >

                        <input
                            type="checkbox"
                            class="remove-image-checkbox"
                            name="remove_images[]"
                            value="{{ $image->id }}"
                            id="remove-image-{{ $image->id }}"
                        >

                        <button
                            type="button"
                            class="remove-image-button"
                            id="remove-button-{{ $image->id }}"
                            aria-label="Remove this image"
                            title="Remove this image"
                            onclick="toggleImageRemoval({{ $image->id }})"
                        >&times;</button>
                    </div>
                @empty
                    <p class="sizes-help">
                        No images have been uploaded for this product.
                    </p>
                @endforelse
            </div>

            {{-- Add More Images --}}
            <div class="field">
                <label for="images">Add More Images</label>

                <div class="upload-box">
                    <p>
                        Select up to 5 new images. Maximum 2 MB each.
                        Existing images will be kept unless marked for removal.
                    </p>

                    <input
                        type="file"
                        name="images[]"
                        id="images"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                    >
                </div>

                <div class="preview-grid" id="preview-grid"></div>
            </div>

            {{-- Actions --}}
            <div class="actions">
                <a href="{{ url('/admin/products') }}" class="btn btn-light">
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary">
                    Update Product
                </button>
            </div>

        </div>
    </form>
</div>

<script>
    function toggleImageRemoval(id) {
        const checkbox  = document.getElementById('remove-image-' + id);
        const imageItem = document.getElementById('image-item-' + id);
        const button    = document.getElementById('remove-button-' + id);

        if (!checkbox || !imageItem || !button) {
            return;
        }

        checkbox.checked = !checkbox.checked;
        imageItem.classList.toggle('marked-for-removal', checkbox.checked);

        if (checkbox.checked) {
            button.innerHTML = '&#8634;';
            button.title = 'Keep this image';
            button.setAttribute('aria-label', 'Keep this image');
        } else {
            button.innerHTML = '&times;';
            button.title = 'Remove this image';
            button.setAttribute('aria-label', 'Remove this image');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

        const imageInput  = document.getElementById('images');
        const previewGrid = document.getElementById('preview-grid');

        imageInput.addEventListener('change', function () {
            previewGrid.innerHTML = '';

            const files = Array.from(this.files);

            if (files.length > 5) {
                alert('You can select up to 5 new images at a time.');
                this.value = '';
                return;
            }

            files.forEach(function (file) {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    const item = document.createElement('div');
                    item.className = 'preview-item';

                    const image = document.createElement('img');
                    image.src = event.target.result;
                    image.alt = 'New image preview';

                    item.appendChild(image);
                    previewGrid.appendChild(item);
                };

                reader.readAsDataURL(file);
            });
        });

        const form            = document.getElementById('productForm');
        const audienceSelect  = document.getElementById('audience_id');
        const categorySelect  = document.getElementById('category_id');
        const sizesSection    = document.getElementById('sizes-section');
        const sizesContainer  = document.getElementById('sizes-container');
        const sizesError      = document.getElementById('sizes-error');
        const sizesHelp       = document.getElementById('sizes-help');
        const totalStockInput = document.getElementById('stock');
        const clearButton     = document.getElementById('clear-sizes');

        const categoriesUrl = @json(
            route('admin.audiences.categories', ['audience' => '__ID__'])
        );

        // { size_id: stock } - the product's saved sizes, or the
        // submitted values after a validation error.
        const initialVariants = @json((object) old('variants', $currentVariants->all()));

        let loadedCategories = [];
        let categoryHasVariants = false;

        function updateTotalStock() {
            let total = 0;

            sizesContainer
                .querySelectorAll('.variant-stock:not(:disabled)')
                .forEach(function (input) {
                    const n = Number(input.value);

                    if (input.value !== '' && Number.isInteger(n) && n >= 1) {
                        total += n;
                    }
                });

            totalStockInput.value = total;
        }
        function renderSizes(category, restoreValues) {
            sizesContainer.innerHTML = '';
            sizesError.textContent = '';
            sizesSection.style.display = 'block';
            categoryHasVariants = Boolean(
                category &&
                Array.isArray(category.sizes) &&
                category.sizes.length > 0
            );

            if (!categoryHasVariants) {
                sizesHelp.style.display = 'none';
                clearButton.style.display = 'none';
                sizesContainer.innerHTML =
                    '<p>No sizes apply to this category. Enter the total stock below.</p>';
                totalStockInput.readOnly = false;
                totalStockInput.required = true;
                return;
            }

            sizesHelp.style.display = '';
            clearButton.style.display = '';
            totalStockInput.readOnly = true;
            totalStockInput.required = false;
            totalStockInput.value = 0;

            category.sizes.forEach(function (size) {
                const id = String(size.id);

                const restore =
                    restoreValues &&
                    Object.prototype.hasOwnProperty.call(initialVariants, id);

                // Row
                const row = document.createElement('div');
                row.className = 'size-row';

                // Checkbox + size name
                const label = document.createElement('label');

                const checkbox = document.createElement('input');
                checkbox.type      = 'checkbox';
                checkbox.className = 'variant-checkbox';
                checkbox.checked   = restore;

                const sizeText = document.createElement('span');
                sizeText.textContent = size.value;

                label.appendChild(checkbox);
                label.appendChild(sizeText);

                // Stock input (only enabled inputs are submitted)
                const quantity = document.createElement('input');
                quantity.type        = 'number';
                quantity.className   = 'input variant-stock';
                quantity.name        = 'variants[' + id + ']';
                quantity.min         = '1';
                quantity.step        = '1';
                quantity.placeholder = 'Enter stock';
                quantity.required    = true;
                quantity.disabled    = !restore;

                if (restore) {
                    quantity.value = initialVariants[id];
                }

                // Checkbox toggled
                checkbox.addEventListener('change', function () {
                    quantity.disabled = !checkbox.checked;
                    quantity.value    = '';
                    quantity.setCustomValidity('');

                    if (checkbox.checked) {
                        quantity.focus();
                    }

                    sizesError.textContent = '';
                    updateTotalStock();
                });

                // Stock typed
                quantity.addEventListener('input', function () {
                    const n = Number(quantity.value);

                    if (quantity.value === '' || !Number.isInteger(n) || n < 1) {
                        quantity.setCustomValidity('Enter a whole number greater than zero.');
                    } else {
                        quantity.setCustomValidity('');
                    }

                    updateTotalStock();
                });

                row.appendChild(label);
                row.appendChild(quantity);
                sizesContainer.appendChild(row);
            });

            updateTotalStock();
        }

        async function loadCategories(audienceId, selectedCategoryId, restore) {
            loadedCategories = [];
            sizesSection.style.display = 'none';
            sizesContainer.innerHTML = '';
            totalStockInput.readOnly = true;
            totalStockInput.required = false;

            if (!audienceId) {
                categorySelect.innerHTML = '<option value="">Select Category</option>';
                categorySelect.disabled = false;
                return;
            }

            categorySelect.disabled = true;

            try {
                const response = await fetch(
                    categoriesUrl.replace('__ID__', encodeURIComponent(audienceId)),
                    { headers: { 'Accept': 'application/json' } }
                );

                if (!response.ok) {
                    throw new Error('Request failed: ' + response.status);
                }

                loadedCategories = await response.json();

                categorySelect.innerHTML = '<option value="">Select Category</option>';

                loadedCategories.forEach(function (category) {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categorySelect.appendChild(option);
                });

                if (selectedCategoryId) {
                    categorySelect.value = String(selectedCategoryId);

                    const selected = loadedCategories.find(function (c) {
                        return String(c.id) === String(selectedCategoryId);
                    });

                    if (selected) {
                        renderSizes(selected, restore);
                    }
                }
            } catch (error) {
                console.error('Category loading failed:', error);
                categorySelect.innerHTML =
                    '<option value="">Could not load categories</option>';
            } finally {
                categorySelect.disabled = false;
            }
        }

        audienceSelect.addEventListener('change', function () {
            loadCategories(this.value, '', false);
        });

        categorySelect.addEventListener('change', function () {
            const categoryId = String(this.value);

            sizesSection.style.display = 'none';
            sizesContainer.innerHTML = '';
            sizesError.textContent = '';

            if (!categoryId) {
                return;
            }

            const category = loadedCategories.find(function (c) {
                return String(c.id) === categoryId;
            });

            renderSizes(category, false);
        });

        clearButton.addEventListener('click', function () {
            sizesContainer.querySelectorAll('.variant-checkbox').forEach(function (box) {
                box.checked = false;
            });

            sizesContainer.querySelectorAll('.variant-stock').forEach(function (input) {
                input.disabled = true;
                input.value = '';
                input.setCustomValidity('');
            });

            sizesError.textContent = '';
            totalStockInput.value = 0;
        });

        // Block submit if no size is picked or a stock value is invalid.
        form.addEventListener('submit', function (event) {
            if (!categoryHasVariants) {
                return;
            }

            const selected = sizesContainer.querySelectorAll('.variant-checkbox:checked');

            if (selected.length === 0) {
                event.preventDefault();
                sizesSection.style.display = 'block';
                sizesError.textContent = 'Please select at least one size.';
                return;
            }

            for (const box of selected) {
                const quantity = box.closest('.size-row').querySelector('.variant-stock');
                const n = Number(quantity.value);

                if (quantity.value === '' || !Number.isInteger(n) || n < 1) {
                    event.preventDefault();
                    quantity.setCustomValidity('Enter a whole number greater than zero.');
                    quantity.reportValidity();
                    return;
                }

                quantity.setCustomValidity('');
            }
        });

        // ON PAGE LOAD: show the saved category's sizes, pre-checked
        loadCategories(
            audienceSelect.value,
            @json($currentCategoryId),
            true
        );
    });
</script>

@endsection