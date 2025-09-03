@extends('template.layout')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show w-50" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">

    <!-- Data Meja -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Data Meja</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.qrs.printAll') }}" target="_blank">
                        <button class="btn btn-success">Print QR</button>
                    </a>
                    <form action="{{ route('admin.tables.store') }}" method="POST">
                        @csrf
                        <button class="btn btn-primary">Tambah Meja</button>
                    </form>
                </div>

            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No Meja</th>
                            <th>Status</th>
                            <th>Kode</th>
                            <th>Kode QR</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tables as $table)
                        <tr>
                            <td>{{ $table->number }}</td>
                           <td>
                                <select class="form-select form-select-sm status-select" data-id="{{ $table->id }}">
                                    <option value="0" {{ $table->status == 0 ? 'selected' : '' }}>Kosong</option>
                                    <option value="1" {{ $table->status == 1 ? 'selected' : '' }}>Dipakai</option>
                                    <option value="2" {{ $table->status == 2 ? 'selected' : '' }}>Telah digunakan</option>
                                </select>
                            </td>
                            <td>{{ $table->code }}</td>
                            <td>
                                {!! QrCode::size(100)->generate(url('/').'?ref='.$table->code) !!}
                            </td>
                            <td>
                                <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus?')">Hapus</button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('.status-select').on('change', function(){
        let tableId = $(this).data('id');
        let status = $(this).val();
        let badge = $(this).siblings('.status-badge');

        $.ajax({
            url: `/admin/tables/${tableId}/status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response){
                if(response.success){
                    badge
                        .removeClass()
                        .addClass('badge mt-1 d-block status-badge bg-' + response.status_badge)
                        .text(response.status_label);
                } else {
                    alert('Gagal memperbarui status');
                }
            },
            error: function(xhr){
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    });
});
</script>
