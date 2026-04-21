<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ImageHelper;

class CustomerController extends Controller
{
    public function create()
    {
        return view('backend.v_customer.create', [
            'judul' => 'Tambah Customer',
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'hp' => 'required|numeric|digits_between:10,13',
            'alamat' => 'required',
            'pos' => 'required',
            'foto' => 'image|mimes:jpeg,jpg,png,gif|max:1024',
        ];
        $messages = [
            'foto.image' => 'Format gambar gunakan jpeg, jpg, png atau gif.',
            'foto.max' => 'Ukuran foto maksimal 1MB.',
            'hp.numeric' => 'HP hanya angka.',
            'hp.digits_between' => 'HP 10-13 digit.',
        ];
        $validatedData = $request->validate($rules, $messages);

        $validatedData['role'] = 2; // Customer role
        $validatedData['status'] = $request->status ?? 1;

        // Create User first
        $user = User::create($validatedData);

        // Handle foto
        if ($request->file('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $filename = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'img-customer/';
            ImageHelper::uploadAndResize($file, $directory, $filename, 385, 400);
            $user->foto = $filename;
            $user->save();
        }

        // Create Customer
        Customer::create([
            'user_id' => $user->id,
            'hp' => $validatedData['hp'],
            'alamat' => $validatedData['alamat'],
            'pos' => $validatedData['pos'],
        ]);

        return redirect()->route('backend.customer.index')->with('success', 'Customer baru berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $user = $customer->user;

        $rules = [
            'nama' => 'required|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'hp' => 'required|numeric|digits_between:10,13',
            'alamat' => 'required',
            'pos' => 'required',
            'foto' => 'image|mimes:jpeg,jpg,png,gif|max:1024',
        ];
        $messages = [
            'foto.image' => 'Format gambar gunakan jpeg, jpg, png atau gif.',
            'foto.max' => 'Ukuran foto maksimal 1MB.',
            'hp.numeric' => 'HP hanya angka.',
            'hp.digits_between' => 'HP 10-13 digit.',
        ];
        $validatedData = $request->validate($rules, $messages);

        $validatedData['status'] = $request->status ?? $user->status;

        // Update user fields
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        }


        if ($request->file('foto')) {
            // Delete old foto
            if ($user->foto) {
                $oldPath = public_path('storage/img-customer/' . $user->foto);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $filename = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'img-customer/';
            ImageHelper::uploadAndResize($file, $directory, $filename, 385, 400);
            $validatedData['foto'] = $filename;
        }

        $user->update($validatedData);

        // Update customer specific
        $customer->update([
            'hp' => $validatedData['hp'],
            'alamat' => $validatedData['alamat'],
            'pos' => $validatedData['pos'],
        ]);

        return redirect()->route('backend.customer.index')->with('success', 'Customer berhasil diupdate');
    }

    // Redirect ke Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback dari Google
    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
            $registeredUser = User::where('email', $socialUser->getEmail())->first();
            if (!$registeredUser) {
                $user = User::create([
                    'nama' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'role' => '2',
                    'status' => true,
                    'password' => Hash::make('default_password'),
                    'hp' => '081234567890',
                ]);
                Customer::create([
                    'user_id' => $user->id,
                    'google_id' => $socialUser->getId(),
                    'google_token' => $socialUser->token
                ]);
                Auth::login($user);
            } else {
                Auth::login($registeredUser);
            }
            return redirect()->intended('beranda');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Terjadi kesalahan saat login dengan Google: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

    public function index()
    {
        $customer = Customer::orderBy('id', 'desc')->get();
        return view('backend.v_customer.index', [
            'judul' => 'Customer',
            'sub' => 'Halaman Customer',
            'index' => $customer
        ]);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('backend.v_customer.show', [
            'judul' => 'Customer',
            'sub' => 'Detail Customer',
            'show' => $customer
        ]);
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('backend.v_customer.edit', [
            'judul' => 'Customer',
            'sub' => 'Edit Customer',
            'edit' => $customer
        ]);
    }


    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $user = User::findOrFail($customer->user_id);
        if ($user->foto) {
            $oldImagePath = public_path('storage/img-customer/' . $user->foto);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        $customer->delete();
        $user->delete();
        return redirect()->route('backend.customer.index')->with('success', 'Data berhasil dihapus');
    }

    public function akun($id)
    {
        $loggedInCustomerId = Auth::user()->id;
        if ($id != $loggedInCustomerId) {
            return redirect()->route('customer.akun', ['id' => $loggedInCustomerId])->with('msgError', 'Anda tidak berhak mengakses akun ini.');
        }
        $customer = Customer::where('user_id', $id)->firstOrFail();
        return view('v_customer.edit', [
            'judul' => 'Customer',
            'subJudul' => 'Akun Customer',
            'edit' => $customer
        ]);
    }

    public function updateAkun(Request $request, $id)
    {
        $customer = Customer::where('user_id', $id)->firstOrFail();
        $rules = [
            'nama' => 'required|max:255',
            'hp' => 'required|min:10|max:13',
            'foto' => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ];
        $messages = [
            'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max' => 'Ukuran file gambar Maksimal adalah 1024 KB.'
        ];
        if ($request->email != $customer->user->email) {
            $rules['email'] = 'required|max:255|email|unique:users';
        }
        if ($request->alamat != $customer->alamat) {
            $rules['alamat'] = 'required';
        }
        if ($request->pos != $customer->pos) {
            $rules['pos'] = 'required';
        }
        $validatedData = $request->validate($rules, $messages);
        if ($request->file('foto')) {
            if ($customer->user->foto) {
                $oldImagePath = public_path('storage/img-customer/' . $customer->user->foto);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'storage/img-customer/';
            ImageHelper::uploadAndResize($file, $directory, $originalFileName, 385, 400);
            $validatedData['foto'] = $originalFileName;
        }
        $customer->user->update($validatedData);
        $customer->update([
            'alamat' => $request->input('alamat'),
            'pos' => $request->input('pos'),
        ]);
        return redirect()->route('customer.akun', $id)->with('success', 'Data berhasil diperbarui');
    }
}
