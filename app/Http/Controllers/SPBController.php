<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SPBController extends Controller
{
    public function index()
    {
        $data = DB::table('spb')->leftJoin('surat', 'spb.surat_id', '=', 'surat.surat_id')->get();
        $list_surat = DB::table('surat')->leftJoin('spb', 'spb.surat_id', '=', 'surat.surat_id')->select([
                'surat.*',
                'spb.nama_file_spb',
        ])->whereNull('nama_file_spb')->where(['surat.status' => "arsip"])->get();
        return view('spb', compact('data','list_surat'));
    }

    public function store(Request $request){
        // dd($request);
        if($request->hasFile('file')){
            $file = $request->file;
            // dd($file->getClientMimeType());
            if(in_array($file->getClientMimeType(),['image/jpg','image/jpeg','image/png','image/svg','application/zip','application/xls','application/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/pdf'])){
                $file_name = round(microtime(true) * 1000).'-'.str_replace(' ','-',$file->getClientOriginalName());
                // $name = Auth::user()->pegawai_id;
                $file->move(public_path('document/spb/'), $file_name);
                // array_push($nama_file_surat, $file_name);
                $data = [
                    'nama_file_spb' => $file_name,
                    'surat_id' => $request->surat_id,
                    'created_by' => Auth::user()->id,
                    'created_at' => now(),
                ];
                DB::table('spb')->insert($data);
                return Redirect::back()->with(['success' => 'Data Berhasil Di Simpan!']);
            }else{
                $error = $file->getClientOriginalName()."File anda tidak dapat kami simpan cek kembali extensi dan besar filenya"."<br>";
                return Redirect::back()->with(['error' => $error]);
            }
        }
        return Redirect::back()->with(['error' => "Data Gagal Disimpan"]);
    }
}
