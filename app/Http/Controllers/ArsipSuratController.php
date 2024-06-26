<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ArsipSuratController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        if($user_id == 192 || $user_id == 459){
            $user_id = [205,192,459];
        }else{
            $user_id = [Auth::user()->id];
        }
        // dd($user_id);
        $list_surat = DB::table('surat')
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.updated_at')
        ->whereIn('surat.updated_by',$user_id)
        ->where(['surat.status' => "arsip"])
        ->get();
        $surat = 'arsipsurat';
        // dd($list_surat);
        return view('monitoring.detail', compact('list_surat','surat'));
    }

}
