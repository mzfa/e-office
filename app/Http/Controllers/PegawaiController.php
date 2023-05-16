<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PegawaiController extends Controller
{
    public function index()
    {
        $data = DB::table('pegawai')
        ->leftJoin('bagian', 'pegawai.bagian_id', '=', 'bagian.bagian_id')
        ->leftJoin('profesi', 'pegawai.profesi_id', '=', 'profesi.profesi_id')
        ->select([
            'pegawai.nama_pegawai',
            'pegawai.nip',
            'bagian.nama_bagian',
            'profesi.nama_profesi'
        ])
        ->whereNull('pegawai.deleted_at')
        ->get();
        return view('pegawai', compact('data'));
    }

    public function sync()
    {
        // dd("ok");
        // $list_pegawai_phis = DB::connection('PHIS-V2')
        // ->table('pegawai')
        // ->select([
        //     'pegawai_id',
        //     'input_time',
        //     'input_user_id',
        //     'mod_time',
        //     'mod_user_id',
        //     'status_batal',
        //     'nama_pegawai',
        //     'nip',
        //     'bagian_id',
        //     'profesi_id'
        // ])
        // ->orderBy('pegawai_id')
        // ->get();
        $list_pegawai_hcm = DB::connection('HCM')
        ->table('pegawai')
        ->get();
        // dd($list_pegawai_phis);

        foreach ($list_pegawai_hcm as $pegawai) {
            
            $datanya[] = [
                'pegawai_id' => $pegawai->pegawai_id,
                'created_at' => $pegawai->created_at,
                'created_by' => $pegawai->created_by,
                'updated_at' => $pegawai->updated_at,
                'updated_by' => $pegawai->updated_by,
                'deleted_at' => $pegawai->deleted_at,
                'deleted_by' => $pegawai->deleted_by,
                'nama_pegawai' => $pegawai->nama_pegawai,
                'nip' => $pegawai->nip,
                'bagian_id' => $pegawai->bagian_id,
                'profesi_id' => $pegawai->profesi_id
            ];
        }

        DB::table('pegawai')->truncate();
        DB::table('pegawai')->insert($datanya);

        $pegawai_detail_hcm = DB::connection('HCM')
        ->table('pegawai_detail')
        ->get();
        
        foreach ($pegawai_detail_hcm as $detail) {
            $datanya2[] = [
                'pegawai_detail_id' => $detail->pegawai_detail_id,
                'pegawai_id' => $detail->pegawai_id,
                'struktur_id' => $detail->struktur_id,
            ];
        }
        // dd($datanya2);
        DB::table('pegawai_detail')->truncate();
        DB::table('pegawai_detail')->insert($datanya2);

        return Redirect::back()->with(['success' => 'Data Berhasil Di Perbarui!']);

        // return redirect()->back()->with('status', ['success', 'Data berhasil disimpan']);
    }
}
