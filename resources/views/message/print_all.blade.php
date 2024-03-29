@extends('layouts.tamplate')

@section('content')
    <div class="card card-primary card-outline">
        <!-- /.card-header -->
        <div class="card-body p-0">
            <div class="mailbox-read-info">
                @if($surat->status == "batal")
                    <div class="alert alert-danger">
                        <h5><i class="icon fas fa-ban"></i> Mohon Maaf Surat Sudah Di Batalkan!</h5>
                    </div>
                @elseif($surat->status == "acc")
                    <div class="alert alert-success">
                        <h5><i class="icon fas fa-check"></i> Surat Sudah Di Arsipkan!</h5>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12 text-center">
                    </div>
                </div>
                <table>
                    <tr>
                        <td width="30%"><img src="{{ asset(env('APP_LOGO')) }}" style="width: 90%" alt=""></td>
                        <th align="left">
                            <h2>Nota Dinas</h2>
                            <h5>{{ $surat->no_surat }}</h5>
                        </th>
                    </tr>
                </table>
                <hr>
                <table>
                    <tr>
                        <th>Kepada Yth</th>
                        <td>&nbsp;</td>
                        <td>:</td>
                        <td>
                            @php
                                $penerima_id = explode('|', $surat->penerima_id);
                                // dump($penerima_id);
                                for ($i = 0; $i < count($penerima_id); $i++) {
                                    $id = $penerima_id[$i];
                                    if ($penerima_id[$i] > 0) {
                                        $user = DB::table('users')
                                            ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
                                            ->where(['users.id' => $id])
                                            ->first();
                                        echo $user->nama_pegawai . ' , ';
                                    }
                                }
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <th>Diteruskan ke</th>
                        <td>&nbsp;</td>
                        <td>:</td>
                        <td>
                            @php
                                $cc = explode('|', $surat->cc);
                                // dump($penerima_id);
                                for ($i = 0; $i < count($cc); $i++) {
                                    $id = $cc[$i];
                                    if ($cc[$i] > 0) {
                                        $user = DB::table('users')
                                            ->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')
                                            ->where(['users.id' => $id])
                                            ->first();
                                        echo $user->nama_pegawai . ' , ';
                                    }
                                }
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <th>Dari</th>
                        <td>&nbsp;</td>
                        <td>:</td>
                        <td>{{ $surat->nama_pegawai }}</td>
                    </tr>
                    <tr>
                        <th>Perihal</th>
                        <td>&nbsp;</td>
                        <td>:</td>
                        <td>{{ $surat->judul_surat }}</td>
                    </tr>
                    <tr>
                        <th>Lampiran</th>
                        <td>&nbsp;</td>
                        <td>:</td>
                        <td>@if(isset($lampiran[0])) Terlampir @else - @endif</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>&nbsp;</td>
                        <td>:</td>
                        <td>{{ date('d-M-Y H:i:s', strtotime($surat->created_at)) }}</td>
                    </tr>
                </table>
            </div>
            <!-- /.mailbox-controls -->
            <div class="mailbox-read-message">
                {!! $surat->isi_surat !!}
            </div>
            <div class="card-footer bg-white">
                {{-- <h2>Lampiran</h2> --}}
                <div class="row">
                    @foreach($lampiran as $item)
                    <div class="col-md-3 col-4">
                        <span class="mailbox-attachment-icon"><i class="far fa-file"></i></span>

                        <div class="mailbox-attachment-info">
                            <a href="{{ 'https://e-office-file.rsumumpekerja-kbn.com/'.$item->nama_file }}" target="_blank" class="mailbox-attachment-name"><i class="fas fa-paperclip"></i> {{ $item->nama_file }}</a>
                            {{-- <span class="mailbox-attachment-size clearfix mt-1">
                                <span>1,245 KB</span>
                                <a href="#" class="btn btn-default btn-sm float-right"><i
                                        class="fas fa-eye"></i></a>
                            </span> --}}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @php
                $datapenerima = explode('|', $surat->penerima_id);
                $hitung = count($datapenerima) - 2;
                // dump($datapenerima[$hitung]);
            @endphp
            @foreach ($surat_balasan as $balas)
                <hr>
                <div class="mailbox-read-message pl-5">
                    <u>
                        <em class="bg-warning">Dijawab : {{ $balas->nama_pegawai }}</em><br>
                        <em>No Disposisi : {{ $balas->nomor_disposisi }}</em>
                    </u><br>
                    {{-- <u><h5>Diteruskan : {{ $balas->nama_pegawai }}</h5></u> --}}
                    <em>{{ \Carbon\Carbon::parse($balas->created_at)->diffForHumans() }}</em><br><br>
                    {!! $balas->isi_balasan !!}
                    @php
                    $id_balasan = $balas->surat_balasan_id;
                    $lampiran_balasan = DB::table('file_balasan')->where(['surat_balasan_id' => $id_balasan])->get();
                    @endphp
                    <div class="row text-right">
                        @foreach($lampiran_balasan as $item)
                        <div class="col-md-3 col-4">
                        <span class="mailbox-attachment-icon"><i class="far fa-file"></i></span>
                        
                        <div class="mailbox-attachment-info">
                            <a href="{{ 'https://e-office-file.rsumumpekerja-kbn.com/'.$item->nama_file_balasan }}" target="_blank" class="mailbox-attachment-name"><i class="fas fa-paperclip"></i> {{ $item->nama_file_balasan }}</a>
                            {{-- <span class="mailbox-attachment-size clearfix mt-1">
                                <span>1,245 KB</span>
                                <a href="#" class="btn btn-default btn-sm float-right"><i
                                    class="fas fa-eye"></i></a>
                                </span> --}}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <!-- /.mailbox-read-message -->
        </div>
        <hr>
        <center>
            Di Cetak pada : {{ date('l, d-m-Y H:i:s') }} Oleh {{ Auth::user()->name }}
        </center>
        <!-- /.card-footer -->
    </div>
@endsection

@push('scripts')
    <script>
        $('.select2').select2()
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
        // $.ajax({
        //     type: 'get',
        //     url: "{{ url('message/tambah_balasan') }}/" + id,
        //     // data:{'id':id}, 
        //     success: function(tampil) {
        //         // console.log(tampil); 
        //         $('#tampildata').html(tampil);
        //         $('#editModal').modal('show');
        //     }
        // })
        window.print();
    </script>
@endpush
