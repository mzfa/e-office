<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index()
    {
        $data = DB::table('users')
        ->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')
        ->leftJoin('hakakses', 'user_akses.hakakses_id', '=', 'hakakses.hakakses_id')
        ->select([
            'users.*',
            'user_akses.jenis_akses',
            'hakakses.nama_hakakses',
        ])->whereNull('users.deleted_at')->get();
        return view('user', compact('data'));
    }

    public function sync()
    {
        $list_user = DB::connection('PHIS-V2')
            ->table('users')
            ->select([
                'user_id',
                'user_name',
                'user_password',
                'nama_pegawai',
                'pegawai_id',
                'status_batal',
            ])
            ->orderBy('user_id')
            ->get();
        $userid = Auth::user()->id;
        $datanya[] = [
            'id' => 0,
            'created_by' => $userid,
            'created_at' => now(),
            'deleted_by' => null,
            'deleted_at' => null,
            'username' => 'mzfa',
            'password' => 'mzfa123',
            'name' => 'mzfa',
            'pegawai_id' => 0,
        ];
        foreach ($list_user as $item) {
            if ($item->status_batal) {
                $deleted_at = $pegawai->mod_time ?? now();
                $deleted_by = $pegawai->mod_user_id ?? 1;
            } else {
                $deleted_at = null;
                $deleted_by = null;
            }
            $datanya[] = [
                'id' => $item->user_id,
                'created_by' => $userid,
                'created_at' => now(),
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'username' => $item->user_name,
                'password' => $item->user_password,
                'name' => $item->nama_pegawai,
                'pegawai_id' => $item->pegawai_id,
            ];
        }
        // dd($datanya);

        DB::table('users')->truncate();
        DB::table('users')->insert($datanya);
        return Redirect::back()->with('message', ['success', 'Data berhasil disimpan']);

        // return redirect()->back()->with('status', ['success', 'Data berhasil disimpan']);
    }

    public function edit($id)
    {
        // $id = Crypt::decrypt($id);
        // dd($data);
        $text = "Data tidak ditemukan";
        $hakakses = DB::table('hakakses')->get();
        if($data = DB::table('users')->leftJoin('user_akses', 'users.id', '=', 'user_akses.user_id')->where(['users.id' => $id])->first()){
            // dd($data);
            $text = '<div class="mb-3 row">'.
                        '<input type="hidden" name="user_id" value="'.Crypt::encrypt($id).'">'.
                        '<input type="hidden" name="user_akses_id" value="'.Crypt::encrypt($data->user_akses_id).'">'.
                        '<label for="staticEmail" class="col-sm-12 col-form-label">Hak Akses</label>'.
                        '<div class="col-sm-12">'.
                        '<select class="form-control" name="hakakses_id">'. 
                        '<option></option>';
                        foreach ($hakakses as $value) {
                            $text .= '<option value="'.$value->hakakses_id.'">'.$value->nama_hakakses.'</option>';
                        }
                        $text .= '</select>'.
                        '</div>'.
                    '</div>'
                    // '<div class="mb-3 row">'.
                    //     '<label for="staticEmail" class="col-sm-12 col-form-label">Jenis Akses</label>'.
                    //     '<div class="col-sm-12">'.
                    //     '<select class="form-control" name="jenis_akses">'. 
                    //         '<option></option>'.
                    //     '</select>'.
                    //     '</div>'.
                    // '</div>'
                    ;
        }
        return $text;
        // return view('admin.hakakses.edit');
    }
    public function update(Request $request){
        $request->validate([
            'user_id' => ['required'],
            'hakakses_id' => ['required'],
            // 'jenis_akses' => ['required', 'string'],
        ]);
        $user_id = Crypt::decrypt($request->user_id);
        $data = [
            'user_id' => $user_id,
            'hakakses_id' => $request->hakakses_id,
            // 'jenis_akses' => $request->jenis_akses,
        ];
        $user_akses_id = Crypt::decrypt($request->user_akses_id);
        if(isset($user_akses_id)){
            // dd($request);
            DB::table('user_akses')->where(['user_akses_id' => $user_akses_id])->update($data);
        }else{
            DB::table('user_akses')->insert($data);
        }
        return Redirect::back()->with(['success' => 'Data Berhasil Di Ubah!']);
    }
}
