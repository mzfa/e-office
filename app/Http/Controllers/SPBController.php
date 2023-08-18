<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Dcblogdev\Dropbox\Facades\Dropbox;
use Illuminate\Support\Facades\Storage;

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
        if($request->hasFile('file')){
            $file = $request->file;
            if(in_array($file->getClientMimeType(),['image/jpg',
            'image/jpeg','image/png','image/svg','application/zip',
            'application/xls','application/xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/pdf'])){
                $filenamewithextension = $request->file('file')->getClientOriginalName();
                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                //get file extension
                $extension = $request->file('file')->getClientOriginalExtension();
                //filename to store
                $filenametostore = round(microtime(true) * 1000).'-'.$filename.'_'.uniqid().'.'.$extension;
                Storage::disk('ftp')->put($filenametostore, fopen($request->file('file'), 'r+'));
                //Upload File to external server
                // $file_name = round(microtime(true) * 1000).'-'.str_replace(' ','-',$file->getClientOriginalName());
         
                // dd($hasil);
                // $name = Auth::user()->pegawai_id;
                // $file->move(public_path('document/spb/'), $file_name);
                // $path = '/file_spb';
                // $file = $request->file('file');
                // // upload file
                // $hasil1 = Dropbox::files()->upload($path, $file);
                // rename
                // $fromPath = $path . '/' . $file->getFilename();
                // $toPath = $path . '/' . $file_name;
                // $hasil = Dropbox::files()->move($fromPath, $toPath);
                // dd($hasil,$hasil1);
                // array_push($nama_file_surat, $file_name);
                $data = [
                    'nama_file_spb' => $filenametostore,
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
