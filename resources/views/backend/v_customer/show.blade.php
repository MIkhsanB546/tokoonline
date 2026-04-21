@extends('backend.v_layouts.app')
@section('content')
    <!-- contentAwal -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $judul }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Customer</label>
                                    <input type="text" value="{{ $show->user->nama ?? ($show->nama ?? '') }}"
                                        class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" value="{{ $show->user->email ?? '' }}" class="form-control"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label>No. HP</label>
                                    <input type="text" value="{{ $show->hp ?? '' }}" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea class="form-control" disabled>{{ $show->alamat ?? '' }}</textarea>
                                </div>
                                @if ($show->foto)
                                    <div class="form-group">
                                        <label>Foto Profil</label> <br>
                                        <img src="{{ asset('storage/' . $show->foto) }}" class="foto-preview" width="100%"
                                            style="max-height: 300px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode POS</label>
                                    <input type="text" value="{{ $show->pos ?? '' }}" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <input type="text" value="{{ $show->status ? 'Aktif' : 'Nonaktif' }}"
                                        class="form-control {{ $show->status ? 'bg-success' : 'bg-danger' }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <input type="text" value="{{ $show->role ?? ($show->user->role ?? '') }}"
                                        class="form-control" disabled>
                                </div>
                                {{-- <div class="form-group">
                                    <label>Google ID</label>
                                    <input type="text" value="{{ $show->google_id ?? 'Tidak ada' }}"
                                        class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Google Token</label>
                                    <textarea class="form-control" disabled style="font-size: 0.8em;">{{ $show->google_token ?? 'Tidak ada' }}</textarea>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="border-top">
                        <div class="card-body">
                            <a href="{{ route('backend.customer.index') }}">
                                <button type="button" class="btn btn-secondary">Kembali</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- contentAkhir -->
@endsection
