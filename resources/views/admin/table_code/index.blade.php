@extends('template.layout')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show w-50" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <!-- Data QR untuk Meja -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Data QR - Meja {{ $table->number }}</h5>
                <form action="{{ route('admin.tables.qrs.store', $table->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Tambah QR</button>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle text-center">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>QR</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qrs as $qr)
                        <tr>
                            <td><strong>{{ $qr->code }}</strong></td>
                            <td>
                                {!! QrCode::size(100)->generate(url('/').'?ref='.$qr->code) !!}
                            </td>
                            <td>
                                <span class="badge bg-{{ $qr->status_badge }}">
                                    {{ $qr->status_label }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.tables.qrs.destroy', [$table->id, $qr->id]) }}"
                                    method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus QR ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($qrs->isEmpty())
                        <tr>
                            <td colspan="4" class="text-muted">Belum ada QR untuk meja ini.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
