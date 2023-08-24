<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;


class MessageController extends Controller
{
    public function index()
    {        
        $user_id = Auth::user()->id;
        $inbox = DB::table('surat')
        ->whereNull('surat.deleted_at')
        ->whereNull('surat.status')
        ->whereNotNull('surat.no_surat')
        // ->where(['surat.user_id' => $user_id])
        ->where('surat.penerima_id', 'like', '%|'. $user_id .'|')
        ->count();
        // dd($inbox);
        $arsip = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        ->where(['surat.status' => "arsip"])
        ->count();
        $terkirim = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        // ->where(['surat.status' => "arsip"])
        ->orderByDesc('surat.created_at')
        ->whereNotNull('surat.no_surat')
        ->count();
        $draft = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        ->whereNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->count();
        $pencarian = "|".Auth::user()->id."|";
        $terusan = DB::table('surat')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->where('cc', 'like', '%'. $pencarian .'%')
        ->count();
        // dd($list_surat);
        return view('message.index', compact('inbox','arsip','terkirim','draft','terusan'));
    }

    public function tulis(){

        $user_id = Auth::user()->id;

        $user_akses = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->where(['users.id' => $user_id])
        ->first();
        
        // $gabung_array = [];
        // foreach($pecah_array as $gabung){
        //     array_push($gabung_array,$gabung);
        // }
        // dd($pecah_array, $gabung_array);
        // $gabung_array = implode(',', $pecah_array);
        $list_bagian = DB::table('users')
        ->select([
            'users.name',
            'users.username',
            'struktur.nama_struktur',
            'struktur.akronim',
            'struktur.parent_id',
        ])
        // ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        // ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        // ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->leftJoin('pegawai_detail', 'users.pegawai_id', '=', 'pegawai_detail.pegawai_id')
        ->leftJoin('struktur', 'pegawai_detail.struktur_id', '=', 'struktur.struktur_id')
        ->where(['users.id' => $user_id])
        ->first();
        $pecah_array = explode('|', $user_akses->akses_bagian);
        $parent_id = $list_bagian->parent_id;
 

        $list_penerima = DB::table('users')
        ->select('users.id','hakakses.nama_hakakses','users.name')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->leftJoin('pegawai_detail', 'users.pegawai_id', '=', 'pegawai_detail.pegawai_id')
        ->leftJoin('struktur', 'pegawai_detail.struktur_id', '=', 'struktur.struktur_id')
        ->whereIn('hakakses.hakakses_id', $pecah_array)
        ->orWhere('struktur.struktur_id', $parent_id)
        ->get();
        
        // SELECT users.id, hakakses.nama_hakakses. users.name FROM users left join user_akses on users.id=user_akses.user_id LEFT JOIN hakakses ON hakakses.hakakses_id=user_akses.hakakses_id LEFT JOIN pegawai_detail ON users.pegawai_id=pegawai_detail.pegawai_id LEFT JOIN struktur ON pegawai_detail.struktur_id=struktur.struktur_id
        // ->whereNotNull('user_akses.hakakses_id')
        // ->whereNull('users.deleted_at')
        // dd($parent_id,$pecah_array,$list_penerima);

        
        // dd($list_bagian);
        // $list_bagian = DB::table('users')->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')->leftJoin('bagian', 'pegawai.bagian_id', '=', 'bagian.bagian_id')->where(['users.id' => $user_id])->first();
        // dd($list_bagian);
        return view('message.tulis',compact('list_penerima','list_bagian'));
    }
    public function edit($id){
        $user_id = Auth::user()->id;
        $list_penerima = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->whereNotNull('user_akses.hakakses_id')
        ->whereNull('users.deleted_at')
        ->get();
        $surat = DB::table('surat')
        ->where(['surat.surat_id' => $id])
        ->whereNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->first();
        $list_bagian = DB::table('users')->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')->leftJoin('bagian', 'pegawai.bagian_id', '=', 'bagian.bagian_id')->where(['users.id' => $user_id])->first();
        return view('message.edit',compact('list_penerima','surat','list_bagian'));
    }
    public function inbox(Request $request){
        $pencarian = $request->pencarian;
        // dd($pencarian)
        $user_id = Auth::user()->id;
        $list_surat = DB::table('surat')
        ->where('surat.penerima_id', 'like', '%|'. $user_id .'|%')
        ->where('surat.judul_surat', 'like', '%'. $pencarian .'%')
        ->whereNull('surat.status')
        ->where(function ($query) {
            $query->where('surat.deleted_at', '>', Carbon::now()->subDays(7)->toDateTimeString())
                ->orWhereNull('deleted_at');
        })
        // ->whereDate('created_at','<', Carbon::now()->subDays(1))
        // ->whereNull('surat.deleted_by')
        // ->orWhere('surat.deleted_at', '>', Carbon::now()->subDays(30)->toDateTimeString())
        // ->where('surat.deleted_at', '<=', Carbon::now()->subDays(1)->toDateTimeString())
        ->whereNotNull('surat.created_by')
        ->orderByDesc('surat.created_at')
        ->get();

        // dd($list_surat,Carbon::now()->subDays(1)->toDateTimeString());
        // dd($list_surat);
        return view('message.inbox', compact('list_surat'));
    }
    public function cetak_barcode($id)
    {
        $id = Crypt::decrypt($id);
        $surat = DB::table('surat')
        ->leftJoin('users', 'users.id', '=', 'surat.user_id')
        ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->where(['surat.surat_id' => $id])
        ->first();
        // dd($surat);
        return view('message.cetak_barcode', compact('surat'));
    }   
    public function sent(Request $request){
        $user_id = Auth::user()->id;
        $pencarian = $request->pencarian;
        $list_surat = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        ->whereNotNull('surat.created_by')
        // ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->where('surat.judul_surat', 'like', '%'. $pencarian .'%')
        ->get();
        return view('message.sent', compact('list_surat'));
    }
    public function read($id){
        $user_id = Auth::user()->id;
        $surat = DB::table('surat')
        ->select([
            'surat.*',
            'pegawai.nama_pegawai',
            'users.username',
        ])
        ->leftJoin('users', 'users.id', '=', 'surat.user_id')
        ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->where(['surat.surat_id' => $id])
        // ->whereNull('surat.status')
        ->first();
        $cek_balasan = DB::table('surat_balasan')->where(['surat_balasan.surat_id' => $id])->where(['surat_balasan.user_id' => $user_id])->first();
        $surat_balasan = DB::table('surat_balasan')
        ->leftJoin('users', 'users.id', '=', 'surat_balasan.user_id')
        ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->select([
            'pegawai.nama_pegawai',
            'users.name',
            'surat_balasan.*',
        ])
        // ->leftJoin('users', 'users.id', '=', 'surat.user_id')
        ->where(['surat_balasan.surat_id' => $id])
        ->whereNull('surat_balasan.deleted_at')
        ->get();

        // $list_bagian = DB::table('users')->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')->leftJoin('bagian', 'pegawai.bagian_id', '=', 'bagian.bagian_id')->where(['users.id' => $user_id])->first();
        $list_bagian = DB::table('users')
        ->select([
            'users.name',
            'users.username',
            'struktur.nama_struktur',
            'struktur.akronim',
            'struktur.parent_id',
        ])
        ->leftJoin('pegawai_detail', 'users.pegawai_id', '=', 'pegawai_detail.pegawai_id')
        ->leftJoin('struktur', 'pegawai_detail.struktur_id', '=', 'struktur.struktur_id')
        ->where(['users.id' => $user_id])
        ->first();

        $user_akses = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->where(['users.id' => $user_id])
        ->first();

        $pecah_array = explode('|', $user_akses->akses_bagian);
        $parent_id = $list_bagian->parent_id;
        $list_penerima = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->leftJoin('pegawai_detail', 'users.pegawai_id', '=', 'pegawai_detail.pegawai_id')
        ->leftJoin('struktur', 'pegawai_detail.struktur_id', '=', 'struktur.struktur_id')
        ->whereNotNull('hakakses.hakakses_id')
        ->whereNotNull('struktur.struktur_id')
        ->get();
        

        $lampiran = DB::table('file')
        ->where(['surat_id' => $id])
        ->get();
        
        // $lampiran = DB::table('file')
        // ->where(['surat_id' => $id])
        // ->get();
        // dd($surat);
        return view('message.read', compact('surat','list_penerima','surat_balasan','cek_balasan','lampiran','list_bagian'));
    }
    public function draft(Request $request){
        $pencarian = $request->pencarian;
        $user_id = Auth::user()->id;
        $list_surat = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        ->whereNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->where('surat.judul_surat', 'like', '%'. $pencarian .'%')
        ->get();
        return view('message.draft',compact('list_surat'));
    }
    public function terusan(Request $request){
        $pencarian = "|".Auth::user()->id."|";
        $list_surat = DB::table('surat')
        // ->whereNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->where('cc', 'like', '%'. $pencarian .'%')
        ->get();
        // dd($list_surat);
        return view('message.terusan',compact('list_surat'));
    }
    public function arsipOpen(){
        $user_id = Auth::user()->id;
        $list_surat = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        ->where(['surat.status' => "arsip"])
        ->orderByDesc('surat.created_at')
        ->get();
        return view('message.arsip',compact('list_surat'));
    }
    public function batal($id){
        $id = Crypt::decrypt($id);
        $data = [
            'deleted_at' => now(),
            'deleted_by' => Auth::user()->id,
            'status' => 'batal',
            'change_status_id' => Auth::user()->id,
        ];
        // dd($data);
        DB::table('surat')->where(['surat.surat_id' => $id])->update($data);
        return Redirect::back()->with(['success' => 'Surat Berhasil di batalkan!']);
    }
    public function arsip($id){
        $id = Crypt::decrypt($id);
        $data = [
            'updated_at' => now(),
            'updated_by' => Auth::user()->id,
            'status' => 'arsip',
            'change_status_id' => Auth::user()->id,
        ];
        // dd($data);
        DB::table('surat')->where(['surat.surat_id' => $id])->update($data);
        return Redirect::back()->with(['success' => 'Surat Berhasil di arsip!']);
    }
    public function aktifkan($id){
        $id = Crypt::decrypt($id);
        $data = [
            'deleted_at' => null,
            'deleted_by' => null,
            'status' => null,
            'change_status_id' => null,
        ];
        // dd($data);
        DB::table('surat')->where(['surat.surat_id' => $id])->update($data);
        return Redirect::back()->with(['success' => 'Surat Berhasil di Aktifkan!']);
    }
    public function nomorotomatis($bagian){
        // $user_id = Auth::user()->id;
        $data = DB::select("SELECT max(no) as nourut FROM surat WHERE bagian='$bagian'");
        $notis = 1;
        if(isset($data[0]->nourut)){
            $notis = $data[0]->nourut + 1;
        }
        $notis .= '/MEMO/'.$bagian.'/'.date('m/Y');
        return $notis;
    }
    public function nomordisposisi($bagian){
        // $user_id = Auth::user()->id;
        $data = DB::select("SELECT max(no) as nourut FROM surat_balasan WHERE disposisi_bagian='$bagian'");
        $notis = 1;
        if(isset($data[0]->nourut)){
            $notis = $data[0]->nourut + 1;
        }
        $notis .= '/DIS/'.$bagian.'/'.date('m/Y');
        return $notis;
    }
    public function reply(Request $request){
        // dd($request);
        $penerima = $request->penerima_id;
        // foreach($request->penerima_id as $penerima_id){
        //     $penerima .= $penerima_id.'|';
        // }
        $error = "";
        $nama_file_surat = [];
        if($request->hasFile('file')){
            $semua_file = "";
            foreach($request->file as $file){
                // dd($file->getClientMimeType());
                if(in_array($file->getClientMimeType(),['image/jpg','image/jpeg','image/png','image/svg','application/zip','application/xls','application/docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/pdf','application/vnd.ms-excel'])){
                    // $file_name = round(microtime(true) * 1000).'-'.str_replace(' ','-',$file->getClientOriginalName());
                    // $name = Auth::user()->pegawai_id;
                    // $file->move(public_path('document/lampiran/'), $file_name);
                    $filenamewithextension = $file->getClientOriginalName();
                    //get filename without extension
                    $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                    //get file extension
                    $extension = $file->getClientOriginalExtension();
                    //filename to store
                    $filenametostore = round(microtime(true) * 1000).'-'.$filename.'_'.uniqid().'.'.$extension;
                    Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));
                    array_push($nama_file_surat, $filenametostore);
                }else{
                    $error .= $file->getClientOriginalName()."File anda tidak dapat kami simpan cek kembali extensi dan besar filenya"."<br>";
                }
            }
            // dd($nama_file_surat);
            if($error !== ""){
                return Redirect::back()->with(['error' => $error]);
            }
            
        }
        $no_surat = app('App\Http\Controllers\MessageController')->nomordisposisi($request->bagian);
        $no = explode('/', $no_surat);
        $surat_id = Crypt::decrypt($request->surat_id);
        $penerima_id = Crypt::decrypt($request->penerima_sebelumnya).$penerima.'|';
        DB::table('surat')->where(['surat.surat_id' => $surat_id])->update([
            'penerima_id' => $penerima_id,
        ]);
        $data = [
            'created_by' => Auth::user()->id,
            'created_at' => now(),
            'user_id' => Auth::user()->id,
            'isi_balasan' => $request->pesan,
            'surat_id' => $surat_id,
            'penerima_balasan_id' => $penerima,
            'no' => $no[0],
            'disposisi_bagian' => $request->bagian,
            'nomor_disposisi' => $no_surat,
        ];
        $store_surat = DB::table('surat_balasan')->insertGetId($data);
        // DB::table('surat_balasan')->insert($data);
        foreach($nama_file_surat as $file_surat){
            $data1 = [
                'nama_file_balasan' => $file_surat,
                'surat_balasan_id' => $store_surat,
            ];
            DB::table('file_balasan')->insert($data1);
        }
        return Redirect::back()->with(['success' => 'Surat Berhasil di kirim!']);
    }
    public function store(Request $request){
        $request->validate([
            'penerima_id' => ['required'],
            'judul' => ['required'],
            'pesan' => 'required',
        ]);
        $cc = "|";
        if(isset($request->cc)){
            foreach($request->cc as $itemcc){
                $cc .= $itemcc."|";
            }
        }
        // dd($cc);
        // dd($request->bagian);
        $no_surat = app('App\Http\Controllers\MessageController')->nomorotomatis($request->bagian);
        $no = explode('/', $no_surat);
        // dd($no[0]);
        if(isset($request->simpan)){
            $error = "";
            $nama_file_surat = [];
            if($request->hasFile('file')){

                $semua_file = "";
                foreach($request->file as $file){
                    // dd($file);
                    if(in_array($file->getClientMimeType(),['image/jpg','image/jpeg','image/png','image/svg','application/zip','application/xls','application/docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/pdf','application/vnd.ms-excel'])){
                        // $file_name = round(microtime(true) * 1000).'-'.str_replace(' ','-',$file->getClientOriginalName());
                        // $name = Auth::user()->pegawai_id;
                        // $file->move(public_path('document/lampiran/'), $file_name);
                        $filenamewithextension = $file->getClientOriginalName();
                        //get filename without extension
                        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                        //get file extension
                        $extension = $file->getClientOriginalExtension();
                        //filename to store
                        $filenametostore = round(microtime(true) * 1000).'-'.$filename.'_'.uniqid().'.'.$extension;
                        Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));
                        array_push($nama_file_surat, $filenametostore);
                    }else{
                        $error .= $file->getClientOriginalName()."File anda tidak dapat kami simpan cek kembali extensi dan besar filenya"."<br>";
                    }
                }
                // dd($nama_file_surat);
                if($error !== ""){
                    // dd($file->getClientMimeType());
                    return Redirect::back()->with(['error' => $error]);
                }
                
            }
            $penerima = "|".$request->penerima_id."|";
            $data = [
                'created_by' => Auth::user()->id,
                'user_id' => Auth::user()->id,
                'created_at' => now(),
                'penerima_id' => $penerima,
                'judul_surat' => $request->judul,
                'isi_surat' => $request->pesan,
                'bagian' => $request->bagian,
                'no_surat' => $no_surat,
                'no' => $no[0],
                'cc' => $cc,
            ];
            $store_surat = DB::table('surat')->insertGetId($data);
            
            foreach($nama_file_surat as $file_surat){
                $data1 = [
                    'nama_file' => $file_surat,
                    'surat_id' => $store_surat,
                ];
                DB::table('file')->insert($data1);
            }

            $datanotif = [
                'user_id' => $request->penerima_id,
                'status' => 0,
                'surat_id' => $store_surat,
            ];
            DB::table('notif')->insert($datanotif);
            return Redirect::back()->with(['success' => 'Surat Berhasil di kirim!']);
        }else{
            // $penerima = "|";
            // foreach($request->penerima_id as $penerima_id){
                //     $penerima .= $penerima_id.'|';
                // }
            $penerima = "|".$request->penerima_id."|";
            $data = [
                'created_at' => now(),
                'user_id' => Auth::user()->id,
                'penerima_id' => $penerima,
                'judul_surat' => $request->judul,
                'isi_surat' => $request->pesan,
            ];
            // dd($data);
            DB::table('surat')->insert($data);
            return Redirect::back()->with(['success' => 'Surat Berhasil di simpan!']);
        }
        // return view('message.trash');
    }
    public function update(Request $request){
        $request->validate([
            'penerima_id' => ['required'],
            'judul' => ['required'],
            'pesan' => 'required'
        ]);
        $cc = "|";
        if(isset($request->cc)){
            foreach($request->cc as $itemcc){
                $cc .= $itemcc."|";
            }
        }
        // dd($request);
        $id = $request->surat_id;
        $no_surat = app('App\Http\Controllers\MessageController')->nomorotomatis($request->bagian);
        $no = explode('/', $no_surat);
        if(isset($request->simpan)){
            // $penerima = "|";
            // foreach($request->penerima_id as $penerima_id){
            //     $penerima .= $penerima_id.'|';
            // }
            $error = "";
            $nama_file_surat = [];
            if($request->hasFile('file')){
                $semua_file = "";
                foreach($request->file as $file){
                    // dd($file->getClientMimeType());
                    if(in_array($file->getClientMimeType(),['image/jpg','image/jpeg','image/png','image/svg','application/zip','application/xls','application/docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/pdf','application/vnd.ms-excel'])){
                        // $file_name = round(microtime(true) * 1000).'-'.str_replace(' ','-',$file->getClientOriginalName());
                        // $name = Auth::user()->pegawai_id;
                        // $file->move(public_path('document/lampiran/'), $file_name);
                        $filenamewithextension = $file->getClientOriginalName();
                        //get filename without extension
                        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                        //get file extension
                        $extension = $file->getClientOriginalExtension();
                        //filename to store
                        $filenametostore = round(microtime(true) * 1000).'-'.$filename.'_'.uniqid().'.'.$extension;
                        Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));
                        array_push($nama_file_surat, $filenametostore);
                    }else{
                        $error .= $file->getClientOriginalName()."File anda tidak dapat kami simpan cek kembali extensi dan besar filenya"."<br>";
                    }
                }
                // dd($nama_file_surat);
                if($error !== ""){
                    return Redirect::back()->with(['error' => $error]);
                }
                
            }
            $penerima = "|".$request->penerima_id."|";
            $data = [
                'created_by' => Auth::user()->id,
                'user_id' => Auth::user()->id,
                'created_at' => now(),
                'penerima_id' => $penerima,
                'judul_surat' => $request->judul,
                'isi_surat' => $request->pesan,
                'bagian' => $request->bagian,
                'no_surat' => $no_surat,
                'no' => $no[0],
                'cc' => $cc,
            ];
            DB::table('surat')->where(['surat.surat_id' => $id])->update($data);
            
            foreach($nama_file_surat as $file_surat){
                $data1 = [
                    'nama_file' => $file_surat,
                    'surat_id' => $id,
                ];
                DB::table('file')->insert($data1);
            }
            
            return Redirect::back()->with(['success' => 'Surat Berhasil di kirim!']);
        }else{
            $penerima = "|".$request->penerima_id."|";
            $data = [
                'created_at' => now(),
                'user_id' => Auth::user()->id,
                'penerima_id' => $penerima,
                'judul_surat' => $request->judul,
                'isi_surat' => $request->pesan,
                // 'bagian' => $request->profesi,
                // 'no_surat' => $no_surat,
                // 'no' => $no,
            ];
            DB::table('surat')->where(['surat.surat_id' => $id])->update($data);
            return Redirect::back()->with(['success' => 'Surat Berhasil di simpan!']);
        }
    }

    public function print_all($id){
        $id = Crypt::decrypt($id);
        $user_id = Auth::user()->id;
        $surat = DB::table('surat')
        ->select([
            'surat.*',
            'pegawai.nama_pegawai',
            'users.username',
        ])
        ->leftJoin('users', 'users.id', '=', 'surat.user_id')
        ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->where(['surat.surat_id' => $id])
        // ->whereNull('surat.status')
        ->first();
        $cek_balasan = DB::table('surat_balasan')->where(['surat_balasan.surat_id' => $id])->where(['surat_balasan.user_id' => $user_id])->first();
        $surat_balasan = DB::table('surat_balasan')
        ->leftJoin('users', 'users.id', '=', 'surat_balasan.user_id')
        ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->select([
            'pegawai.nama_pegawai',
            'users.name',
            'surat_balasan.*',
        ])
        // ->leftJoin('users', 'users.id', '=', 'surat.user_id')
        ->where(['surat_balasan.surat_id' => $id])
        ->whereNull('surat_balasan.deleted_at')
        ->get();

        // $list_bagian = DB::table('users')->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')->leftJoin('bagian', 'pegawai.bagian_id', '=', 'bagian.bagian_id')->where(['users.id' => $user_id])->first();
        $list_bagian = DB::table('users')
        ->select([
            'users.name',
            'users.username',
            'struktur.nama_struktur',
            'struktur.akronim',
            'struktur.parent_id',
        ])
        ->leftJoin('pegawai_detail', 'users.pegawai_id', '=', 'pegawai_detail.pegawai_id')
        ->leftJoin('struktur', 'pegawai_detail.struktur_id', '=', 'struktur.struktur_id')
        ->where(['users.id' => $user_id])
        ->first();

        $user_akses = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->where(['users.id' => $user_id])
        ->first();

        $pecah_array = explode('|', $user_akses->akses_bagian);
        $parent_id = $list_bagian->parent_id;
        $list_penerima = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->leftJoin('pegawai_detail', 'users.pegawai_id', '=', 'pegawai_detail.pegawai_id')
        ->leftJoin('struktur', 'pegawai_detail.struktur_id', '=', 'struktur.struktur_id')
        ->whereIn('hakakses.hakakses_id', $pecah_array)
        ->orWhere('struktur.struktur_id', $parent_id)
        ->get();

        $lampiran = DB::table('file')
        ->where(['surat_id' => $id])
        ->get();
        
        // $lampiran = DB::table('file')
        // ->where(['surat_id' => $id])
        // ->get();
        // dd($surat);
        return view('message.print_all', compact('surat','list_penerima','surat_balasan','cek_balasan','lampiran','list_bagian'));
    }
}
