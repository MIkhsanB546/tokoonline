@extends('v_layouts.app')

@section('content')
    <div class="row">

        <div class="col-md-12">

            <div class="billing-details">

                <small>HISTORY</small>

                <div class="section-title">
                    <h3 class="title">HISTORY PESANAN</h3>
                </div>

                {{-- ALERT --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">

                    <table class="shopping-cart-table table">

                        <thead>
                            <tr>
                                <th>ID PESANAN</th>
                                <th>TANGGAL</th>
                                <th>TOTAL BAYAR</th>
                                <th>STATUS</th>
                                <th>DETAIL</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($orders as $item)
                                <tr>

                                    <td>
                                        {{ $item->id }}
                                    </td>

                                    <td>
                                        {{ $item->created_at->format('d M Y H:i') }}
                                    </td>

                                    <td>
                                        Rp.
                                        {{ number_format($item->total_harga + $item->biaya_ongkir, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        {{ ucfirst($item->status) }}
                                    </td>

                                    <td>

                                        <a href="#" class="primary-btn">
                                            LIHAT DETAIL
                                        </a>

                                        <a href="#" class="primary-btn">
                                            INVOICE
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center">
                                        Belum ada history pesanan
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection
