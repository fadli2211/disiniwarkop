@extends('template.layout')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Menu</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="row">
                        @csrf
                        @method('PUT')

                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Nama Menu</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $menu->name) }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $menu->price) }}">
                            @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select name="category_id" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="is_available" class="form-label">Tersedia</label>
                            <select name="is_available" class="form-control">
                                <option value="1" {{ old('is_available', $menu->is_available) == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ old('is_available', $menu->is_available) == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                            @error('is_available') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $menu->description) }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="image" class="form-label">Gambar Baru (Opsional)</label>
                            <input type="file" name="image" class="form-control">
                            @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label d-block">Gambar Sekarang</label>
                            @if ($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="menu image" width="100">
                            @else
                                <span class="text-muted">Tidak ada gambar</span>
                            @endif
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">Update Menu</button>
                            <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
