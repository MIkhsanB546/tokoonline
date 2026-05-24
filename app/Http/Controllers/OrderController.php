<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Produk;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function addToCart($id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $produk = Produk::findOrFail($id);
        $order = Order::firstOrCreate(
            ['customer_id' => $customer->id, 'status' => 'pending'],
            ['total_harga' => 0]
        );
        $orderItem = OrderItem::firstOrCreate(
            ['order_id' => $order->id, 'produk_id' => $produk->id],
            ['quantity' => 1, 'harga' => $produk->harga]
        );
        if (!$orderItem->wasRecentlyCreated) {
            $orderItem->quantity++;
            $orderItem->save();
        }
        $order->total_harga += $produk->harga;
        $order->save();
        return redirect()->route('order.cart')->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function viewCart()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'paid'])
            ->first();
        if ($order) {
            $order->load('orderItems.produk');
        }
        return view('v_order.cart', compact('order'));
    }

    public function updateCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();;;
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();
        if ($order) {
            $orderItem = $order->orderItems()->where('id', $id)->first();
            if ($orderItem) {
                $quantity = $request->input('quantity');
                if ($quantity > $orderItem->produk->stok) {
                    return redirect()->route('order.cart')->with('error', 'Jumlah produk melebihi stok yang tersedia');
                }
                $order->total_harga -= $orderItem->harga * $orderItem->quantity;
                $orderItem->quantity = $quantity;
                $orderItem->save();
                $order->total_harga += $orderItem->harga * $orderItem->quantity;
                $order->save();
            }
        }
        return redirect()->route('order.cart')->with('success', 'Jumlah produk berhasil diperbarui');
    }

    public function removeFromCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();
        if ($order) {
            $orderItem = OrderItem::where('order_id', $order->id)->where('produk_id', $id)->first();
            if ($orderItem) {
                $order->total_harga -= $orderItem->harga * $orderItem->quantity;
                $orderItem->delete();
                if ($order->total_harga <= 0) {
                    $order->delete();
                } else {
                    $order->save();
                }
            }
        }
        return redirect()->route('order.cart')->with('success', 'Produk berhasil dihapus dari keranjang');
    }

    public function selectShipping(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::with('orderItems.produk')
            ->where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->first();
        if (!$order || $order->orderItems->count() == 0) {
            return redirect()->route('order.cart')->with('error', 'Keranjang belanja kosong.');
        }
        return view('v_order.select_shipping', compact('order'));
    }

    public function updateongkir(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();
        if ($order) {
            // Simpan data ongkir ke dalam order
            $order->kurir = $request->input('kurir');
            $order->layanan_ongkir = $request->input('layanan_ongkir');
            $order->biaya_ongkir = $request->input('biaya_ongkir');
            $order->estimasi_ongkir = $request->input('estimasi_ongkir');
            $order->total_berat = $request->input('total_berat');
            $order->alamat =
                $request->alamat . ', ' .
                $request->city_name . ', ' .
                $request->province_name;
            $order->pos = $request->input('pos');
            $order->save();
            return redirect()->route('order.selectpayment');
        }
        return back()->with('error', 'Gagal menyimpan data ongkir');
    }

    public function selectPayment()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();
        if (!$order) {
            return redirect()->route('order.cart')->with('error', 'Keranjang belanja kosong.');
        }
        return view('v_order.select_payment', compact('order'));
    }

    public function checkout($id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        $order = Order::where('customer_id', $customer->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return back()->with('error', 'Order tidak ditemukan');
        }

        // ubah status order
        $order->status = 'proses';
        $order->save();

        return redirect()->route('order.history')
            ->with('success', 'Pesanan berhasil dibuat');
    }

    public function orderHistory()
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        $orders = Order::where('customer_id', $customer->id)
            ->where('status', '!=', 'pending')
            ->latest()
            ->get();

        return view('v_order.history', compact('orders'));
    }

    public function getProvinces()
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(env('RAJAONGKIR_BASE_URL') . '/destination/province');

        $data = $response->json();

        return response()->json($data['data']);
    }

    public function getCities($provinceId)
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(
                env('RAJAONGKIR_BASE_URL') .
                    '/destination/city/' .
                    $provinceId
            );

        $data = $response->json();

        return response()->json($data['data']);
    }

    public function getDistricts($cityId)
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(env('RAJAONGKIR_BASE_URL') . '/destination/district/' . $cityId);

        $data = $response->json();

        return response()->json($data['data']);
    }

    public function getCost(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required',
        ]);

        $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->post(
                env('RAJAONGKIR_BASE_URL') .
                    '/calculate/domestic-cost',
                [
                    'origin' => $request->origin,
                    'destination' => $request->destination,
                    'origin_type' => 'city',
                    'destination_type' => 'city',
                    'weight' => $request->weight,
                    'courier' => $request->courier,
                    'price' => 'lowest'
                ]
            );

        $data = $response->json();

        return response()->json($data['data']);
    }
}
