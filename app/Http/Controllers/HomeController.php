<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        // dump($list_surat);
        return view('home',compact('user_akses'));
    }

    public function notifikasi()
    {
        $user_id = Auth::user()->id;
        $notif = DB::table('notif')->where(['notif.user_id' => $user_id, 'notif.status' => 0])->first();
        if(isset($notif)){
            $data = [
                'status' => 1,
            ];
            $id = $notif->notif_id;
            DB::table('notif')->where(['notif_id' => $id])->update($data);
            return "berhasil";
        }

    }
}
