@extends('template.layout')
@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show w-50" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Tambah Kategori</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.category.store') }}" method="POST" class="row">
                        @csrf
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Tambah Kategori</button>
                    </form>
                </div>
            </div>
        </div>

         <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Data Kategori</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Nama</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.category.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.category.destroy', $item->id) }}" method="POST" style="display:inline" method="POST">
                                            @csrf
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
        <!-- [ sample-page ] end -->
    </div>
@endsection
