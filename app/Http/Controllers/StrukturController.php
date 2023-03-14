<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class StrukturController extends Controller
{
    public function index()
    {
        $struktur = [];
        $data = DB::table('struktur')->where(['parent_id' => 0])->whereNull('deleted_at')->get();
        // dd($data);
        foreach($data as $key => $item)
        {
            array_push($struktur, [
                'struktur_id' => $item->struktur_id,
                'nama_struktur' => $item->nama_struktur,
                'parent_id' => $item->parent_id,
                'akronim' => $item->akronim,
                'substruktur' => []
            ]);
            $struktur_id = $item->struktur_id;
            $substruktur1 = DB::table('struktur')->where(['parent_id' => $struktur_id])->whereNull('deleted_at')->get();
            // dd($substruktur1);
            foreach($substruktur1 as $key1 => $sub1)
            {
                array_push($struktur[$key]["substruktur"], [
                    "struktur_id" => $sub1->struktur_id,
                    "nama_struktur" => $sub1->nama_struktur,
                    "akronim" => $sub1->akronim,
                    'substruktur1' => [],
                ]);

                $struktur_id1 = $sub1->struktur_id;
                $substruktur2 = DB::table('struktur')->where(['parent_id' => $struktur_id1])->whereNull('deleted_at')->get();
                // dd($struktur['substruktur']);
                foreach($substruktur2 as $key2 => $sub2)
                {
                    // dd($key1);
                    array_push($struktur[$key]["substruktur"][$key1]["substruktur1"], [
                        "struktur_id" => $sub2->struktur_id,
                        "nama_struktur" => $sub2->nama_struktur,
                        "akronim" => $sub2->akronim,
                        'substruktur2' => [],
                    ]);

                    $struktur_id2 = $sub2->struktur_id;
                    $substruktur3 = DB::table('struktur')->where(['parent_id' => $struktur_id2])->whereNull('deleted_at')->get();
                    // dd($struktur['substruktur']);
                    foreach($substruktur3 as $key3 => $sub3)
                    {
                        array_push($struktur[$key]["substruktur"][$key1]["substruktur1"][$key2]["substruktur2"], [
                            "struktur_id" => $sub3->struktur_id,
                            "nama_struktur" => $sub3->nama_struktur,
                            "akronim" => $sub3->akronim,
                            'substruktur3' => [],
                        ]);

                        $struktur_id3 = $sub3->struktur_id;
                        $substruktur4 = DB::table('struktur')->where(['parent_id' => $struktur_id3])->whereNull('deleted_at')->get();
                        // dd($struktur['substruktur']);
                        foreach($substruktur4 as $key4 => $sub4)
                        {
                            array_push($struktur[$key]["substruktur"][$key1]["substruktur1"][$key2]["substruktur2"][$key3]["substruktur3"], [
                                "struktur_id" => $sub4->struktur_id,
                                "nama_struktur" => $sub4->nama_struktur,
                                "akronim" => $sub4->akronim,
                                'substruktur' => [],
                            ]);

                            $struktur_id4 = $sub4->struktur_id;
                            $substruktur5 = DB::table('struktur')->where(['parent_id' => $struktur_id4])->whereNull('deleted_at')->get();
                            // dd($struktur['substruktur']);
                        }
                    }
                }
            }
        }
        // dd($struktur);
        return view('struktur', compact('struktur'));
    }

    public function sync()
    {
        // dd("ok");
        $list_struktur_hcm = DB::connection('HCM')
        ->table('struktur')
        ->get();
        // dd($list_struktur_hcm);
        // dd($list_struktur_phis);
        foreach ($list_struktur_hcm as $struktur) {
            $datanya[] = [
                'struktur_id' => $struktur->struktur_id,
                'created_at' => $struktur->created_at,
                'created_by' => $struktur->created_by,
                'updated_at' => $struktur->updated_at,
                'updated_by' => $struktur->updated_by,
                'deleted_at' => $struktur->deleted_at,
                'deleted_by' => $struktur->deleted_by,
                'nama_struktur' => $struktur->nama_struktur,
                'parent_id' => $struktur->parent_id,
                'akronim' => $struktur->akronim,
            ];
        }

        DB::table('struktur')->truncate();
        DB::table('struktur')->insert($datanya);
        return Redirect::back()->with(['success' => 'Data Berhasil Di Perbarui!']);

        // return redirect()->back()->with('status', ['success', 'Data berhasil disimpan']);
    }
}
