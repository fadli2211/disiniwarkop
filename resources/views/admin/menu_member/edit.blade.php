@extends('template.layout')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Edit Menu Member</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.menu-member.update', $memberItem->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="menu_id" class="form-label">Menu</label>
                            <select name="menu_id" class="form-control">
                                <option value="">-- Pilih Menu --</option>
                                @foreach($menus as $category)
                                <option value="{{ $category->id }}" disabled>
                                    {{ $category->name }}
                                </option>
                                @foreach ($category->menus as $item)
                                <option value="{{ $item->id }}" {{ $memberItem->menu_id == $item->id ? 'selected' : ''
                                    }}>
                                    - {{ $item->name }}
                                </option>
                                @endforeach
                                @endforeach
                            </select>
                            @error('menu_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="point" class="form-label">Point</label>
                            <input type="number" step="1" name="point" class="form-control"
                                value="{{ old('point', $memberItem->point) }}">
                            @error('point') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Edit Menu</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
