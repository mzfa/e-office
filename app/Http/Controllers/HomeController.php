<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $user_akses = DB::table('users')
        ->select([
            'users.name',
            'users.username',
            'struktur.nama_struktur',
            'hakakses.nama_hakakses',
        ])
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        // ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->leftJoin('pegawai_detail', 'users.pegawai_id', '=', 'pegawai_detail.pegawai_id')
        ->leftJoin('struktur', 'pegawai_detail.struktur_id', '=', 'struktur.struktur_id')
        ->where(['users.id' => $user_id])
        ->first();
        // dd($user_akses);
        return view('home',compact('user_akses'));
    }
}
