@extends('template.layout')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show w-50" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Tambah Menu -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Tambah Menu</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="row">
                        @csrf
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Nama Menu</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}">
                            @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select name="category_id" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="is_available" class="form-label">Tersedia</label>
                            <select name="is_available" class="form-control">
                                <option value="1" {{ old('is_available') == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                            @error('is_available') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="image" class="form-label">Gambar</label>
                            <input type="file" name="image" class="form-control">
                            @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mt-4">Tambah Menu</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Menu -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Data Menu</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Gambar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menus as $menu)
                                <tr>
                                    <td>{{ $menu->name }}</td>
                                    <td>Rp{{ number_format($menu->price, 2, ',', '.') }}</td>
                                    <td>{{ $menu->category->name ?? '-' }}</td>
                                    <td>
                                        @if($menu->is_available)
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Tersedia</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($menu->image)
                                            <img src="{{ asset('storage/' . $menu->image) }}" alt="gambar" width="50">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
