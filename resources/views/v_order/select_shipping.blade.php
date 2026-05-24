@extends('v_layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="billing-details">

                <div class="section-title">
                    <h3 class="title">PILIH PENGIRIMAN</h3>
                </div>

                @php
                    $totalBerat = 0;

                    foreach ($order->orderItems as $item) {
                        $totalBerat += $item->produk->berat * $item->quantity;
                    }
                @endphp

                {{-- FORM ONGKIR --}}
                <div class="row">

                    <div class="col-md-12">

                        {{-- PROVINSI --}}
                        <div class="form-group">
                            <label><strong>Provinsi Tujuan:</strong></label>

                            <select name="province" id="province" class="input">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>

                        {{-- KOTA --}}
                        <div class="form-group">
                            <label><strong>Kota Tujuan:</strong></label>

                            <select name="city" id="city" class="input">
                                <option value="">-- Pilih Kota --</option>
                            </select>
                        </div>

                        {{-- KURIR --}}
                        <div class="form-group">
                            <label><strong>Kurir:</strong></label>

                            <select name="courier" id="courier" class="input">
                                <option value="">-- Pilih Kurir --</option>
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                            </select>
                        </div>

                        {{-- ALAMAT --}}
                        <div class="form-group">
                            <label><strong>Alamat</strong></label>

                            <textarea name="alamat" id="alamat" rows="4" class="input">{{ auth()->user()->customer->alamat ?? '' }}</textarea>
                        </div>

                        {{-- KODE POS --}}
                        <div class="form-group">
                            <label><strong>Kode Pos</strong></label>

                            <input type="text" name="pos" id="pos" class="input"
                                value="{{ auth()->user()->customer->pos ?? '' }}">
                        </div>

                        <input type="hidden" id="total_berat" value="{{ $totalBerat }}">

                        {{-- BUTTON --}}
                        <div class="form-group">
                            <button type="button" id="cekOngkir" class="primary-btn">
                                CEK ONGKIR
                            </button>
                        </div>

                    </div>

                </div>

                <br>

                {{-- HASIL ONGKIR --}}
                <div class="table-responsive">

                    <table class="shopping-cart-table table">

                        <thead>
                            <tr>
                                <th>Layanan</th>
                                <th>Biaya</th>
                                <th>Estimasi Pengiriman</th>
                                <th>Total Berat</th>
                                <th>Total Harga</th>
                                <th>Bayar</th>
                            </tr>
                        </thead>

                        <tbody id="resultOngkir">

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </div>

    {{-- JQuery --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function() {

            // LOAD PROVINCES
            $.ajax({
                url: "{{ url('provinces') }}",
                type: "GET",
                dataType: "json",

                success: function(response) {

                    $('#province').html(
                        '<option value="">-- Pilih Provinsi --</option>'
                    );

                    $.each(response, function(key, value) {

                        $('#province').append(`
                            <option value="${value.id}">
                                ${value.name}
                            </option>
                        `);

                    });

                }
            });

            // GET CITIES
            $('#province').change(function() {

                let provinceId = $(this).val();

                $('#city').html(
                    '<option value="">Loading...</option>'
                );

                $.ajax({

                    url: "/cities/" + provinceId,
                    type: "GET",
                    dataType: "json",

                    success: function(response) {

                        $('#city').html(
                            '<option value="">-- Pilih Kota --</option>'
                        );

                        $.each(response, function(key, value) {

                            $('#city').append(`
                                <option value="${value.id}">
                                    ${value.name}
                                </option>
                            `);

                        });

                    }

                });

            });

            // CEK ONGKIR
            $('#cekOngkir').click(function() {

                let origin = 152;
                let destination = $('#city').val();
                let courier = $('#courier').val();
                let weight = $('#total_berat').val();

                if (destination == '' || courier == '') {
                    alert('Pilih kota dan kurir terlebih dahulu');
                    return;
                }

                $.ajax({

                    url: "{{ url('cost') }}",
                    type: "POST",
                    dataType: "json",

                    data: {
                        _token: "{{ csrf_token() }}",
                        origin: origin,
                        destination: destination,
                        courier: courier,
                        weight: weight,
                    },

                    beforeSend: function() {

                        $('#resultOngkir').html(`
                            <tr>
                                <td colspan="6" class="text-center">
                                    Loading...
                                </td>
                            </tr>
                        `);

                    },

                    success: function(response) {

                        $('#resultOngkir').empty();

                        $.each(response, function(key, value) {

                            $('#resultOngkir').append(`

                                <tr>

                                    <td>
                                        ${value.service}
                                    </td>

                                    <td>
                                        Rp ${new Intl.NumberFormat('id-ID')
                                            .format(value.cost)}
                                    </td>

                                    <td>
                                        ${value.etd}
                                    </td>

                                    <td>
                                        ${weight} Gram
                                    </td>

                                    <td>
                                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                    </td>

                                    <td>

                                        <form action="{{ route('order.updateongkir') }}"
                                            method="POST">

                                            @csrf

                                            <input type="hidden"
                                                name="kurir"
                                                value="${courier}">

                                            <input type="hidden"
                                                name="layanan_ongkir"
                                                value="${value.service}">

                                            <input type="hidden"
                                                name="biaya_ongkir"
                                                value="${value.cost}">

                                            <input type="hidden"
                                                name="estimasi_ongkir"
                                                value="${value.etd}">

                                            <input type="hidden"
                                                name="total_berat"
                                                value="${weight}">

                                            <input type="hidden"
                                                name="alamat"
                                                value="${$('#alamat').val()}">

                                            <input type="hidden"
                                                name="pos"
                                                value="${$('#pos').val()}">

                                            <input type="hidden"
                                                name="city_name"
                                                value="${$('#city option:selected').text()}">

                                            <input type="hidden"
                                                name="province_name"
                                                value="${$('#province option:selected').text()}">

                                            <button type="submit"
                                                class="primary-btn">
                                                PILIH PENGIRIMAN
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            `);

                        });

                    },

                    error: function(xhr) {

                        console.log(xhr.responseText);

                        $('#resultOngkir').html(`
                            <tr>
                                <td colspan="6" class="text-center">
                                    Gagal mengambil data ongkir
                                </td>
                            </tr>
                        `);

                    }

                });

            });

        });
    </script>
@endsection
