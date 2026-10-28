@extends('layouts.app')

@section('title', 'Sizes & Variants | Admin')

@section('content')
<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <h1>Sizes & Variants</h1>
    <p>Manage size types and the values available for each type.</p>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <div style="color: red;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Add a size type --}}
    <h2>Add Size Type</h2>

    <form action="{{ route('admin.sizes.store') }}"
          method="POST"
          style="display: flex; gap: 10px; margin: 20px 0;">
        @csrf

        <input type="text" name="name"  placeholder="e.g. Bodywear, Waist Size, Foot Wear, Free size" value="{{ old('name') }}" required>
        <button type="submit">Add Size Type</button>
    </form>

    <hr>

    {{-- List size types and their values --}}
    <h2>Existing Size Types</h2>

    @forelse($sizes as $size)
        <div style="border: 1px solid #ddd; padding: 18px; margin: 18px 0; border-radius: 8px;">

            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                <h3 style="margin: 0;">
                    {{ $size->name }}
                </h3>

                <form
                    action="{{ route('admin.sizes.destroy', $size) }}"
                    method="POST"
                    onsubmit="return confirm('Delete this size type?')"
                >
                    @csrf
                    @method('DELETE')

                    <button type="submit">Delete Size Type</button>
                </form>
            </div>

            {{-- Existing variant values --}}
            <h4>Available Values</h4>

            @if($size->variants->isNotEmpty())
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                    @foreach($size->variants as $variant)
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid #ccc; padding: 8px 12px; border-radius: 6px;">
                            <span>{{ $variant->getAttribute('value') }}</span>

                            <form
                                action="{{ route('admin.sizes.variants.destroy', $variant) }}"
                                method="POST"
                                onsubmit="return confirm('Delete this size value?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" aria-label="Delete {{ $variant->getAttribute('value') }}">×</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No values added yet.</p>
            @endif

            {{-- Add a value to this size type --}}
            <h4>Add a Value</h4>

            <form
                action="{{ route('admin.sizes.variants.store', $size) }}"
                method="POST"
                style="display: flex; gap: 10px; flex-wrap: wrap;"
            >
                @csrf

                <input
                    type="text"
                    name="value"
                    placeholder="e.g. XXXL or 42"
                    value="{{ old('value') }}"
                    maxlength="50"
                    required
                >

                <button type="submit">Add Value</button>
            </form>

        </div>
    @empty
        <p>No size types added yet.</p>
    @endforelse
</div>
@endsection