@extends('v_layouts.app')

@section('content')
    <div class="row">

        <div class="col-md-12">

            <div class="billing-details">

                <div class="section-title">
                    <h3 class="title">KERANJANG BELANJA</h3>
                </div>

                <div class="table-responsive">

                    <table class="shopping-cart-table table">

                        <thead>
                            <tr>
                                <th>PRODUK</th>
                                <th class="text-center">HARGA</th>
                                <th class="text-center">QUANTITY</th>
                                <th class="text-center">TOTAL</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($order->orderItems as $item)
                                <tr>

                                    {{-- PRODUK --}}
                                    <td>

                                        <div class="product product-widget">

                                            <div class="product-thumb">
                                                <img src="{{ asset('storage/img-produk/' . $item->produk->foto) }}"
                                                    alt="" width="60">
                                            </div>

                                            <div class="product-body">

                                                <h3 class="product-price">
                                                    {{ $item->produk->nama_produk }}
                                                </h3>

                                                <p>
                                                    Berat:
                                                    {{ number_format($item->produk->berat, 0, ',', '.') }}
                                                    Gram
                                                </p>

                                                <p>
                                                    Stok:
                                                    {{ $item->produk->stok }}
                                                    Gram
                                                </p>

                                            </div>

                                        </div>

                                    </td>

                                    {{-- HARGA --}}
                                    <td class="text-center">
                                        <strong style="font-size:24px">
                                            Rp.
                                            {{ number_format($item->harga, 0, ',', '.') }}
                                        </strong>
                                    </td>

                                    {{-- QUANTITY --}}
                                    <td class="text-center">
                                        <strong style="font-size:20px">
                                            {{ $item->quantity }}
                                        </strong>
                                    </td>

                                    {{-- TOTAL --}}
                                    <td class="text-center">

                                        <strong style="font-size:24px; color:#F8694A">
                                            Rp.
                                            {{ number_format($item->harga * $item->quantity, 0, ',', '.') }}
                                        </strong>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- TOTAL --}}
                <div class="pull-right" style="width: 450px; margin-top:20px;">

                    <table class="table table-bordered">

                        <tr>
                            <th width="45%">
                                SUBTOTAL
                            </th>

                            <td>
                                <strong style="font-size: 18px">
                                    Rp.
                                    {{ number_format($order->total_harga, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                Ongkos Kirim
                            </th>

                            <td>
                                <strong>
                                    Rp.
                                    {{ number_format($order->biaya_ongkir, 0, ',', '.') }}
                                </strong>

                                <br>

                                {{ strtoupper($order->kurir) }}.
                                {{ $order->layanan_ongkir }}

                                *estimasi
                                {{ $order->estimasi_ongkir }}
                            </td>
                        </tr>

                        <tr>
                            <th>
                                TOTAL BAYAR
                            </th>

                            <td>

                                <strong style="font-size:36px; color:#F8694A">

                                    Rp.
                                    {{ number_format($order->total_harga + $order->biaya_ongkir, 0, ',', '.') }}

                                </strong>

                            </td>
                        </tr>

                    </table>

                    {{-- BUTTON --}}
                    <div class="pull-right">

                        <form action="{{ route('order.checkout', $order->id) }}" method="POST">
                            @csrf

                            <button type="submit" class="primary-btn">
                                BAYAR SEKARANG
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
