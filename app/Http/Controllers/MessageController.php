<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class MessageController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $listInbox = DB::table('surat')
        ->where(['surat.penerima_id' => $user_id])
        ->whereNull('surat.deleted_at')
        ->get();
        return view('message.index', compact('listInbox'));
    }

    public function tulis(){
        $list_penerima = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->whereNotNull('user_akses.hakakses_id')
        ->whereNull('users.deleted_at')
        ->get();

        $list_profesi = DB::table('profesi')->whereNull('profesi.deleted_at')->get();
        return view('message.tulis',compact('list_penerima','list_profesi'));
    }
    public function edit($id){
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
        $list_profesi = DB::table('profesi')->whereNull('profesi.deleted_at')->get();
        return view('message.edit',compact('list_penerima','surat','list_profesi'));
    }
    public function inbox(){
        $user_id = Auth::user()->id;
        $list_surat = DB::table('surat')
        ->where('surat.penerima_id', 'like', '%|'. $user_id .'|%')
        ->whereNull('surat.deleted_at')
        ->get();
        return view('message.inbox', compact('list_surat'));
    }
    public function sent(){
        $user_id = Auth::user()->id;
        $list_surat = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->get();
        return view('message.sent', compact('list_surat'));
    }
    public function read($id){
        $list_penerima = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->whereNotNull('user_akses.hakakses_id')
        ->whereNull('users.deleted_at')
        ->get();
        $user_id = Auth::user()->id;
        $surat = DB::table('surat')
        ->leftJoin('users', 'users.id', '=', 'surat.user_id')
        ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->where(['surat.surat_id' => $id])
        ->whereNull('surat.deleted_at')
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
        // dd($surat);
        return view('message.read', compact('surat','list_penerima','surat_balasan','cek_balasan'));
    }
    public function draft(){
        $user_id = Auth::user()->id;
        $list_surat = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        ->whereNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->get();
        return view('message.draft',compact('list_surat'));
    }
    public function trash(){
        return view('message.trash');
    }
    public function nomorotomatis($bagian){
        $user_id = Auth::user()->id;
        // $user = DB::table('users')
        // ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        // ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        // ->leftJoin('profesi', 'pegawai.profesi_id', '=', 'profesi.profesi_id')
        // ->where(['surat.surat_id' => $user_id])
        // ->first();
        // $profesi = $user->nama_profesi;
        // $surat = DB::table('surat')
        // ->where(['surat.surat_id', 'like', '%|'. $user->nama_profesi .'|%'])
        // // ->whereNull('surat.deleted_at')
        // ->first();
        $data = DB::select("SELECT max(no) as nourut FROM surat WHERE bagian='$bagian'");
        $notis = 1;
        if(isset($data[0]->nourut)){
            $notis = $data[0]->nourut + 1;
        }
        $notis .= '/MEMO/'.$bagian.'/'.date('m/Y');
        return $notis;
    }
    public function reply(Request $request){
        // dd($request);
        $penerima = $request->penerima_id;
        // foreach($request->penerima_id as $penerima_id){
        //     $penerima .= $penerima_id.'|';
        // }
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
        ];
        DB::table('surat_balasan')->insert($data);
        return Redirect::back()->with(['success' => 'Surat Berhasil di kirim!']);
    }
    public function store(Request $request){
        $request->validate([
            'penerima_id' => ['required'],
            'judul' => ['required'],
            'pesan' => 'required',
        ]);
        $no_surat = app('App\Http\Controllers\MessageController')->nomorotomatis($request->profesi);
        $no = explode('/', $no_surat);
        // dd($no[0]);
        if(isset($request->simpan)){
            $penerima = "|".$request->penerima_id."|";
            // foreach($request->penerima_id as $penerima_id){
            //     $penerima .= $penerima_id.'|';
            // }
            $data = [
                'created_by' => Auth::user()->id,
                'user_id' => Auth::user()->id,
                'created_at' => now(),
                'penerima_id' => $penerima,
                'judul_surat' => $request->judul,
                'isi_surat' => $request->pesan,
                'bagian' => $request->profesi,
                'no_surat' => $no_surat,
                'no' => $no[0],
            ];
            // dd($data);
            DB::table('surat')->insert($data);
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
        $id = $request->surat_id;
        $no_surat = app('App\Http\Controllers\MessageController')->nomorotomatis($request->profesi);
        $no = explode('/', $no_surat);
        if(isset($request->simpan)){
            // $penerima = "|";
            // foreach($request->penerima_id as $penerima_id){
            //     $penerima .= $penerima_id.'|';
            // }
            $penerima = "|".$request->penerima_id."|";
            $data = [
                'created_by' => Auth::user()->id,
                'user_id' => Auth::user()->id,
                'created_at' => now(),
                'penerima_id' => $penerima,
                'judul_surat' => $request->judul,
                'isi_surat' => $request->pesan,
                'bagian' => $request->profesi,
                'no_surat' => $no_surat,
                'no' => $no[0],
            ];
            // dd($data);
            DB::table('surat')->where(['surat.surat_id' => $id])->update($data);
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
                'bagian' => $request->profesi,
                'no_surat' => $no_surat,
                'no' => $no,
            ];
            // dd($data);
            DB::table('surat')->where(['surat.surat_id' => $id])->update($data);
            return Redirect::back()->with(['success' => 'Surat Berhasil di simpan!']);
        }
        // return view('message.trash');
    }
    public function sync()
    {
        $list_profesi_phis = DB::connection('PHIS-V2')
            ->table('profesi')
            ->select([
                'profesi_id',
                'input_time',
                'input_user_id',
                'mod_time',
                'mod_user_id',
                'status_batal',
                'nama_profesi'
            ])
            ->orderBy('profesi_id')
            ->get();

        foreach ($list_profesi_phis as $profesi) {
            if ($profesi->status_batal) {
                $deleted_at = $profesi->mod_time ?? now();
                $deleted_by = $profesi->mod_user_id ?? 1;
            } else {
                $deleted_at = null;
                $deleted_by = null;
            }
            $datanya[] = [
                'profesi_id' => $profesi->profesi_id,
                'created_at' => $profesi->input_time,
                'created_by' => $profesi->input_user_id,
                'updated_at' => $profesi->mod_time,
                'updated_by' => $profesi->mod_user_id,
                'deleted_at' => $deleted_at,
                'deleted_by' => $deleted_by,
                'nama_profesi' => $profesi->nama_profesi
            ];
        }

        DB::table('profesi')->truncate();
        DB::table('profesi')->insert($datanya);
        return Redirect::back()->with(['success' => 'Data Berhasil Di Perbarui!']);

        // return redirect()->back()->with('status', ['success', 'Data berhasil disimpan']);
    }
}
