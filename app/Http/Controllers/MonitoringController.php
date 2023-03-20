<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class MonitoringController extends Controller
{
    public function index()
    {
        $menu = [];
        $data = DB::table('menu')->where(['parent_id' => 0])->whereNull('deleted_at')->get();
        // dd($data);
        $unit = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'hakakses.hakakses_id', '=', 'user_akses.hakakses_id')
        ->leftJoin('pegawai', 'pegawai.pegawai_id', '=', 'users.pegawai_id')
        ->leftJoin('bagian', 'bagian.bagian_id', '=', 'pegawai.bagian_id')
        // ->whereIn('hakakses.hakakses_id', $pecah_array)
        ->whereNotNull('user_akses.hakakses_id')
        ->count();
        // dd(Carbon::now());
        $today = DB::table('surat')
        // ->where(['surat.created_at','=', Carbon::now()])
        ->whereDate('created_at', Carbon::today())
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->count();

        $all = DB::table('surat')
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->whereNull('surat.status')
        ->count();
        $batal = DB::table('surat')
        ->whereNotNull('surat.created_by')
        ->whereNotNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->count();

        $pending = DB::table('surat')
        ->where( 'surat.created_at', '<', Carbon::now()->subDays(3))
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.status')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->count();
        $arsip = DB::table('surat')
        ->where(['surat.status' => 'arsip'])
        ->whereNotNull('surat.created_by')
        ->orderByDesc('surat.created_at')
        ->count();

        // foreach($data as $key => $item)
        // {
        //     array_push($menu, [
        //         'menu_id' => $item->menu_id,
        //         'nama_menu' => $item->nama_menu,
        //         'url_menu' => $item->url_menu,
        //         'icon_menu' => $item->icon_menu,
        //         'parent_id' => $item->parent_id,
        //         'submenu' => []
        //     ]);
        //     $menu_id = $item->menu_id;
        //     $submenu = DB::table('menu')->where(['parent_id' => $menu_id])->whereNull('deleted_at')->get();
        //     // dd($submenu);
        //     foreach($submenu as $sub)
        //     {
        //         array_push($menu[$key]["submenu"], [
        //             "menu_id" => $sub->menu_id,
        //             "nama_menu" => $sub->nama_menu,
        //             "url_menu" => $sub->url_menu,
        //             "icon_menu" => $sub->icon_menu,
        //         ]);
        //         // dd($menu['submenu']);
        //     }
        // }
        // dd($menu);
        return view('monitoring.index', compact('menu','unit','today','arsip','pending','all','batal'));
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
        return view('monitoring.read', compact('surat','list_penerima','surat_balasan','cek_balasan','lampiran','list_bagian'));
    }

    public function unit()
    {
        $user_id = Auth::user()->id;
        $struktur = DB::table('struktur')->select(['akronim','nama_struktur'])->distinct()->get(['akronim']);
        return view('monitoring.unit', compact('struktur'));
    }   
    public function perunit($bagian){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $list_surat = DB::table('surat')
        ->where(['surat.bagian' => $bagian])
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->get();
        return view('message.sent', compact('list_surat'));
    }
    public function disposisi()
    {
        $user_id = Auth::user()->id;
        $struktur = DB::table('struktur')->select(['akronim','nama_struktur'])->distinct()->get(['akronim']);
        return view('monitoring.disposisi', compact('struktur'));
    }   
    public function perdisposisi($bagian){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $list_surat = DB::table('surat_balasan')
        ->where(['surat_balasan.disposisi_bagian' => $bagian])
        ->whereNotNull('surat_balasan.created_by')
        ->whereNull('surat_balasan.deleted_at')
        ->orderByDesc('surat_balasan.created_at')
        ->get();
        // dd($list_surat);
        return view('monitoring.list_disposisi', compact('list_surat'));
    }
    public function semuasurat(){
        $list_surat = DB::table('surat')
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->whereNull('surat.status')
        ->get();
        $surat = 'all';
        // dd($list_surat);
        return view('monitoring.detail', compact('list_surat','surat'));
    }
    public function suratdibatalkan(){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $list_surat = DB::table('surat')
        ->whereNotNull('surat.created_by')
        ->whereNotNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->get();
        $surat = 'allbatal';
        // dd($list_surat);
        return view('monitoring.detail', compact('list_surat','surat'));
    }
    public function pencarian(Request $request){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $pencarian = $request->pencarian;
        $surat = $request->surat;
        $list_surat = '';
        if($surat == 'all'){
            $list_surat = DB::table('surat')
            ->whereNotNull('surat.created_by')
            ->whereNull('surat.deleted_at')
            ->orderByDesc('surat.created_at')
            ->whereNull('surat.status')
            ->where('surat.no_surat', 'like', '%'. $pencarian .'%')
            ->get();
        }elseif($surat == 'arsipsurat'){
            $user_id = Auth::user()->id;
            $list_surat = DB::table('surat')
            ->whereNotNull('surat.created_by')
            ->whereNull('surat.deleted_at')
            ->orderByDesc('surat.created_at')
            ->whereNull('surat.status')
            ->where(['surat.updated_by' => $user_id])
            ->where('surat.no_surat', 'like', '%'. $pencarian .'%')
            ->get();
        }elseif($surat == 'allbatal'){
            $user_id = Auth::user()->id;
            $list_surat = DB::table('surat')
            ->whereNotNull('surat.created_by')
            ->whereNotNull('surat.deleted_at')
            ->orderByDesc('surat.created_at')
            ->whereNull('surat.status')
            ->where('surat.no_surat', 'like', '%'. $pencarian .'%')
            ->get();
        }else{
            $list_surat = DB::table('surat')
            ->whereNotNull('surat.created_by')
            ->whereNull('surat.deleted_at')
            ->orderByDesc('surat.created_at')
            ->where(['surat.status' => "arsip"])
            ->where('surat.no_surat', 'like', '%'. $pencarian .'%')
            ->get();
        }
        // dd($list_surat);
        return view('monitoring.pencarian', compact('list_surat','surat'));
    }
    public function today(){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $list_surat = DB::table('surat')
        ->whereDate('created_at', Carbon::today())
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->whereNull('surat.status')
        ->get();
        // dd($list_surat);
        return view('monitoring.today', compact('list_surat'));
    }
    public function pending(){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $list_surat = DB::table('surat')
        ->where( 'surat.created_at', '<', Carbon::now()->subDays(3))
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->whereNull('surat.status')
        ->get();
        // dd($list_surat);
        return view('monitoring.today', compact('list_surat'));
    }

    public function semuaarsipsurat(){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $list_surat = DB::table('surat')
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->where(['surat.status' => "arsip"])
        ->get();
        $surat = 'arsip';
        // dd($list_surat);
        return view('monitoring.detail', compact('list_surat','surat'));
    }
    public function pencarianarsip(Request $request){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $pencarian = $request->pencarian;
        $list_surat = DB::table('surat')
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->where(['surat.status' => "arsip"])
        // ->where('surat.judul_surat', 'like', '%'. $pencarian .'%')
        ->where('surat.no_surat', 'like', '%'. $pencarian .'%')
        ->get();
        // dd($list_surat);
        return view('monitoring.pencarian', compact('list_surat'));
    }
    public function arsip()
    {
        $user_id = Auth::user()->id;
        $struktur = DB::table('struktur')->select(['akronim','nama_struktur'])->distinct()->get(['akronim']);
        return view('monitoring.arsip', compact('struktur'));
    }   
    public function arsip_detail($bagian){
        // $bagian = Auth::user()->id;
        // dd($bagian);
        $list_surat = DB::table('surat')
        ->where(['surat.bagian' => $bagian])
        ->where(['surat.status' => "arsip"])
        ->whereNotNull('surat.created_by')
        ->whereNull('surat.deleted_at')
        ->orderByDesc('surat.created_at')
        ->get();
        return view('message.sent', compact('list_surat'));
    }

    public function store(Request $request){
        $request->validate([
            'nama_menu' => ['required', 'string'],
        ]);
        $parent_id = 0;
        if(isset($request->parent_id)){
            $parent_id = $request->parent_id;
        }
        $data = [
            'nama_menu' => $request->nama_menu,
            'icon_menu' => $request->icon_menu,
            'url_menu' => $request->url_menu,
            'created_by' => Auth::user()->id,
            'created_at' => now(),
            'parent_id' => $parent_id,
        ];

        DB::table('menu')->insert($data);

        return Redirect::back()->with(['success' => 'Data Berhasil Di Simpan!']);
    }

    public function edit($id)
    {
        // $id = Crypt::decrypt($id);
        // dd($data);
        $text = "Data tidak ditemukan";
        if($data = DB::table('menu')->where(['menu_id' => $id])->get()){

            $text = '<div class="mb-3 row">'.
                    '<label for="staticEmail" class="col-sm-2 col-form-label">Nama menu</label>'.
                    '<div class="col-sm-10">'.
                    '<input type="text" class="form-control" id="nama_menu" name="nama_menu" value="'.$data[0]->nama_menu.'" required>'.
                    '</div>'.
                '</div>'.
                '<input type="hidden" class="form-control" id="menu_id" name="menu_id" value="'.Crypt::encrypt($data[0]->menu_id) .'" required>'.
                '<div class="mb-3 row">'.
                    '<label for="staticEmail" class="col-sm-2 col-form-label">Icon</label>'.
                    '<div class="col-sm-10">'.
                    '<input type="text" class="form-control" id="icon_menu" name="icon_menu" value="'.$data[0]->icon_menu.'">'.
                    '</div>'.
                '</div>'.
                '<div class="mb-3 row">'.
                    '<label for="staticEmail" class="col-sm-2 col-form-label">Url</label>'.
                    '<div class="col-sm-10">'.
                    '<input type="text" class="form-control" id="url_menu" name="url_menu" value="'.$data[0]->url_menu.'">'.
                    '</div>'.
                '</div>';
        }
        return $text;
        // return view('admin.menu.edit');
    }

    public function update(Request $request){
        $request->validate([
            'nama_menu' => ['required', 'string'],
        ]);
        $data = [
            'nama_menu' => $request->nama_menu,
            'icon_menu' => $request->icon_menu,
            'url_menu' => $request->url_menu,
            'updated_by' => Auth::user()->id,
            'updated_at' => now(),
        ];
        $menu_id = Crypt::decrypt($request->menu_id);
        DB::table('menu')->where(['menu_id' => $menu_id])->update($data);
        return Redirect::back()->with(['success' => 'Data Berhasil Di Ubah!']);
    }

    public function delete($id){
        $id = Crypt::decrypt($id);
        // if($data = DB::select("SELECT * FROM tbl_menu WHERE menu_id='$id'")){
        //     DB::select("DELETE FROM tbl_menu WHERE menu_id='$id'");
        // }
        $data = [
            'deleted_by' => Auth::user()->id,
            'deleted_at' => now(),
        ];
        DB::table('menu')->where(['menu_id' => $id])->update($data);
        return Redirect::back()->with(['success' => 'Data Berhasil Di Hapus!']);
    }
    
}
