
@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div style="max-width:1000px;margin:0 auto;padding:24px;">
    <h1>Categories</h1>
    <p>Create categories and choose the sizes available for each one.</p>

    <div id="message" style="margin:15px 0;"></div>

    <form id="categoryForm" action="{{ url('/admin/categories') }}" method="POST">
        @csrf

        <div style="display:grid;gap:16px;max-width:600px;">
            <div>
                <label for="size_id">Size Type</label>
                <select name="size_id" id="size_id" required
                    style="display:block;width:100%;padding:10px;">
                    <option value="">Choose size type</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size->id }}">
                            {{ $size->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="name">Category Name</label>
                <input type="text" name="name" id="name"
                    placeholder="e.g. Slippers, Heels, Dress"
                    required maxlength="255"
                    style="display:block;width:100%;padding:10px;">
            </div>

            <div>
                <label for="audience_id">Audience</label>
                <select name="audience_id" id="audience_id" required
                    style="display:block;width:100%;padding:10px;">
                    <option value="">Choose audience</option>
                    @foreach ($audiences as $audience)
                        <option value="{{ $audience->id }}">
                            {{ $audience->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" id="submitButton"
                style="padding:12px 18px;cursor:pointer;">
                Add Category
            </button>
        </div>
    </form>

    <hr style="margin:30px 0;">

    <h2>Existing Categories</h2>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;margin-top:15px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #ddd;">Name</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #ddd;">Audience</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #ddd;">Size Type</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #ddd;">Action</th>
                </tr>
            </thead>

            <tbody id="categoriesTable">
                @forelse ($categories as $category)
                    <tr id="category-{{ $category->id }}">
                        <td style="padding:10px;border-bottom:1px solid #eee;">
                            {{ $category->name }}
                        </td>
                        <td style="padding:10px;border-bottom:1px solid #eee;">
                            {{ $category->audience?->name ?? '-' }}
                        </td>
                        <td style="padding:10px;border-bottom:1px solid #eee;">
                            {{ $category->size?->name ?? '-' }}
                        </td>
                        <td style="padding:10px;border-bottom:1px solid #eee;">
                            <button type="button" class="deleteCategory"
                                data-id="{{ $category->id }}"
                                style="cursor:pointer;">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr id="emptyRow">

                        <td colspan="5" style="padding:12px;">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('categoryForm');
    const message = document.getElementById('message');
    const submitButton = document.getElementById('submitButton');
    const table = document.getElementById('categoriesTable');

    function showMessage(text, isError = false) {
        message.textContent = text;
        message.style.color = isError ? '#b91c1c' : '#15803d';
    }
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        showMessage('');

        if (!form.reportValidity()) return;
        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                const errors = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : data.message || 'Could not add category.';

                showMessage(errors, true);
                return;
            }

            const category = data.category;
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.remove();

            const row = document.createElement('tr');
            row.id = `category-${category.id}`;

            [
                category.name,
                category.audience_name,
                category.size_name,
            ].forEach(value => {
                const cell = document.createElement('td');
                cell.style.cssText =
                    'padding:10px;border-bottom:1px solid #eee;';
                cell.textContent = value;
                row.appendChild(cell);
            });

            const actionCell = document.createElement('td');
            actionCell.style.cssText =
                'padding:10px;border-bottom:1px solid #eee;';

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'deleteCategory';
            deleteButton.dataset.id = category.id;
            deleteButton.textContent = 'Delete';
            deleteButton.style.cursor = 'pointer';

            actionCell.appendChild(deleteButton);
            row.appendChild(actionCell);
            table.prepend(row);

            form.reset();
            showMessage(data.message || 'Category added successfully!');
        } catch (error) {
            showMessage('Something went wrong. Please try again.', true);
            console.error(error);
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Add Category';
        }
    });

    // Event delegation keeps delete working for newly added rows too.
    table.addEventListener('click', async function (event) {
        const button = event.target.closest('.deleteCategory');
        if (!button) return;

        if (!confirm('Are you sure you want to delete this category?')) {
            return;
        }

        button.disabled = true;

        try {
            const response = await fetch(
                `{{ url('/admin/categories') }}/${button.dataset.id}`,
                {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':
                            form.querySelector('[name="_token"]').value
                    },
                    body: new URLSearchParams({ _method: 'DELETE' })
                }
            );

            const data = await response.json();

            if (!response.ok) {
                showMessage(
                    data.message || 'Could not delete category.',
                    true
                );
                button.disabled = false;
                return;
            }

            button.closest('tr').remove();
            showMessage(data.message || 'Category deleted successfully!');

            if (!table.querySelector('tr')) {
                const emptyRow = document.createElement('tr');
                emptyRow.id = 'emptyRow';
                const cell = document.createElement('td');
                cell.colSpan = 5;
                cell.style.padding = '12px';
                cell.textContent = 'No categories yet.';
                emptyRow.appendChild(cell);
                table.appendChild(emptyRow);
            }
        } catch (error) {
            showMessage('Something went wrong while deleting.', true);
            button.disabled = false;
            console.error(error);
        }
    });

    renderVariants('');
});
</script>
@endsection
