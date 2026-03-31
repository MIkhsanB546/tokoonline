<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class BerandaController extends Controller
{
    public function berandaBackend()
    {
        if (!isset(Auth::user()->email)) {
            return redirect(route('backend.login'));
        }

        return view('backend.v_beranda.index', [
            'judul' => 'Halaman Beranda'
        ]);
    }
}
