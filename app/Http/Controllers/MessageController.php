<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ->whereNotNull('user_akses.hakakses_id')
        ->whereNull('users.deleted_at')
        ->get();
        return view('message.tulis',compact('list_penerima'));
    }
    public function inbox(){
        $user_id = Auth::user()->id;
        $listInbox = DB::table('surat')
        ->where(['surat.penerima_id' => $user_id])
        ->whereNull('surat.deleted_at')
        ->get();
        return view('message.inbox', compact('listInbox'));
    }
    public function sent(){
        $user_id = Auth::user()->id;
        $list_surat = DB::table('surat')
        ->where(['surat.user_id' => $user_id])
        ->whereNull('surat.deleted_at')
        ->get();
        return view('message.sent', compact('list_surat'));
    }
    public function read($id){
        $user_id = Auth::user()->id;
        $surat = DB::table('surat')
        ->leftJoin('users', 'users.id', '=', 'surat.user_id')
        ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
        ->where(['surat.surat_id' => $id])
        ->whereNull('surat.deleted_at')
        ->first();
        return view('message.read', compact('surat'));
    }
    public function draft(){
        return view('message.draft');
    }
    public function trash(){
        return view('message.trash');
    }
    public function store(Request $request){
        $request->validate([
            'penerima_id' => ['required'],
            'judul' => ['required'],
            'pesan' => 'required'
        ]);
        if(isset($request->simpan)){
            $penerima = "|";
            foreach($request->penerima_id as $penerima_id){
                $penerima .= $penerima_id.'|';
            }
            $data = [
                'created_by' => Auth::user()->id,
                'user_id' => Auth::user()->id,
                'created_at' => now(),
                'penerima_id' => $penerima,
                'judul_surat' => $request->judul,
                'isi_surat' => $request->pesan,
                'no_surat' => $request->no_surat,
            ];
            // dd($data);
            DB::table('surat')->insert($data);
            return Redirect::back()->with(['success' => 'Surat Berhasil di kirim!']);
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
