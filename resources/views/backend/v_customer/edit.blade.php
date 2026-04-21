@extends('backend.v_layouts.app')
@section('content')
    <!-- contentAwal -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form action="{{ route('backend.customer.updateakun', $edit->id) }}" method="post"
                        enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="card-body">
                            <h4 class="card-title"> {{ $judul }} </h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Foto</label>
                                        @if ($edit->foto || $edit->user->foto)
                                            <img src="{{ asset('storage/img-customer/' . ($edit->foto ?? $edit->user->foto)) }}"
                                                class="foto-preview" width="100%">
                                        @else
                                            <img src="{{ asset('image/img-default.jpg') }}" class="foto-preview"
                                                width="100%">
                                        @endif
                                        <input type="file" name="foto"
                                            class="form-control @error('foto') is-invalid @enderror"
                                            onchange="previewFoto()">
                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" name="nama"
                                            value="{{ old('nama', $edit->user->nama ?? ($edit->nama ?? '')) }}"
                                            class="form-control @error('nama') is-invalid @enderror">
                                        @error('nama')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email"
                                            value="{{ old('email', $edit->user->email ?? '') }}"
                                            class="form-control @error('email') is-invalid @enderror">
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>No. HP</label>
                                        <input type="text" name="hp" value="{{ old('hp', $edit->hp ?? '') }}"
                                            class="form-control @error('hp') is-invalid @enderror">
                                        @error('hp')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $edit->alamat ?? '') }}</textarea>
                                        @error('alamat')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Kode POS</label>
                                        <input type="text" name="pos" value="{{ old('pos', $edit->pos ?? '') }}"
                                            class="form-control @error('pos') is-invalid @enderror">
                                        @error('pos')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                                            <option value=""> - Pilih Status - </option>
                                            <option value="1"
                                                {{ old('status', $edit->status ?? $edit->user->status ? '1' : '0') == '1' ? 'selected' : '' }}>
                                                Aktif</option>
                                            <option value="0"
                                                {{ old('status', $edit->status ?? $edit->user->status ? '1' : '0') == '0' ? 'selected' : '' }}>
                                                Nonaktif</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-top">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary">Perbaharui</button>
                                <a href="{{ route('backend.customer.index') }}"> <button type="button"
                                        class="btn btn-secondary">Kembali</button> </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- contentAkhir -->
@endsection
<script>
    function previewFoto() {
        const fotoInput = document.querySelector('input[name="foto"]');
        const preview = document.querySelector('.foto-preview');
        const file = fotoInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
