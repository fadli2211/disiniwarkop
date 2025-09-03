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
                    <h5>Tambah Menu Member</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.menu-member.store') }}" method="POST" enctype="multipart/form-data" class="row">
                        @csrf

                        <div class="mb-3 col-md-6">
                            <label for="menu_id" class="form-label">Menu</label>
                            <select name="menu_id" class="form-control">
                                <option value="">-- Pilih Menu --</option>
                                @foreach($menus as $category)
                                    <option value="{{ $category->id }}" disabled>
                                        {{ $category->name }}
                                    </option>
                                    @foreach ($category->menus as $item)
                                    <option value="{{ $item->id }}" {{ old('menu_id') == $item->id ? 'selected' : '' }}>
                                        - {{ $item->name }}
                                    </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('menu_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="point" class="form-label">Point</label>
                            <input type="number" step="1" name="point" class="form-control" value="{{ old('point') }}">
                            @error('point') <small class="text-danger">{{ $message }}</small> @enderror
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
                                <th>Poin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($memberItem as $item)
                                <tr>
                                    <td>{{ $item->menu->name }}</td>
                                    <td>{{ $item->point }}</td>
                                    <td>
                                        <a href="{{ route('admin.menu-member.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.menu-member.destroy', $item->id) }}" method="POST" style="display:inline">
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
