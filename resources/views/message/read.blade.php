@extends('layouts.head_read')
@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Read Mail</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-0">
            <div class="mailbox-read-info">
                @if ($surat->status == 'batal')
                    <div class="alert alert-danger">
                        <h5><i class="icon fas fa-ban"></i> Mohon Maaf Surat Sudah Di Batalkan!</h5>
                    </div>
                @elseif($surat->status == 'acc')
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
                        <td width="30%"><img alt="" src="{{ asset(env('APP_LOGO')) }}" style="width: 90%"></td>
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
                        <td>
                            @if (isset($lampiran[0]))
                                Terlampir
                            @else
                                -
                            @endif
                        </td>
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
                    @foreach ($lampiran as $item)
                        <div class="col-md-3 col-4">
                            <span class="mailbox-attachment-icon"><i class="far fa-file"></i></span>
                            <div class="mailbox-attachment-info">
                                <a class="mailbox-attachment-name" href="{{ env('URL_FTP') . $item->nama_file }}" target="_blank"><i class="fas fa-paperclip"></i> {{ $item->nama_file }}</a>
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
                // dump($datapenerima[$hitung2]);
                $disposisi_terakhir = '';
            @endphp
            @foreach ($surat_balasan as $balas)
                <hr>
                <div class="mailbox-read-message text-right">
                    <u>
                        <em class="bg-warning">Dijawab : {{ $balas->nama_pegawai }}</em><br>
                        <em>No Disposisi : {{ $balas->nomor_disposisi }}</em>
                    </u><br>
                    {{-- <u><h5>Diteruskan : {{ $balas->nama_pegawai }}</h5></u> --}}
                    <em>{{ \Carbon\Carbon::parse($balas->created_at)->locale('id')->translatedFormat('l, d F Y H:i:s') }}</em><br><br>
                    {!! $balas->isi_balasan !!}
                    @php
                        $id_balasan = $balas->surat_balasan_id;
                        $lampiran_balasan = DB::table('file_balasan')
                            ->where(['surat_balasan_id' => $id_balasan])
                            ->get();
                    @endphp
                    <div class="row text-right">
                        @foreach ($lampiran_balasan as $item)
                            <div class="col-md-3 col-4">
                                <span class="mailbox-attachment-icon"><i class="far fa-file"></i></span>
                                <div class="mailbox-attachment-info">
                                    <a class="mailbox-attachment-name" href="{{ env('URL_FTP') . $item->nama_file_balasan }}" target="_blank"><i class="fas fa-paperclip"></i> {{ $item->nama_file_balasan }}</a>
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
                    $disposisi_terakhir = $id_balasan;
                @endphp
            @endforeach
            <!-- /.mailbox-read-message -->
        </div>
        <!-- /.card-body -->
        <!-- /.card-footer -->
        <div class="card-footer">
            <div class="float-right">
                {{-- <button type="button" class="btn btn-default"><i class="fas fa-reply"></i> Reply</button> --}}
                @if ($datapenerima[$hitung] == Auth::user()->id && empty($surat->status) && $surat->deleted_at == null)
                    {{-- <a onclick="return confirm('Apakah anda yakin surat ini ingin mengarsipkan surat ini?')" href="{{ url('message/arsip/'.Crypt::encrypt($surat->surat_id)) }}" class="btn btn-success"><i class="fas fa-check-circle"></i> Arsip</a> --}}
                    <button class="btn btn-success" data-target="#arsip-surat" data-toggle="modal" type="button"><i class="fas fa-check-circle"></i> Arsip</button>
                    <button class="btn btn-default" data-target="#reply-surat" data-toggle="modal" type="button"><i class="fas fa-reply"></i> Teruskan</button>
                @endif
                @if ($datapenerima[$hitung - 1] == Auth::user()->id && empty($surat->status) && $surat->deleted_at == null)
                    <a class="btn btn-danger" href="{{ url('message/batal_disposisi/' . Crypt::encrypt($surat->surat_id) . '/' . Crypt::encrypt($disposisi_terakhir)) }}" onclick="return confirm('Apakah anda yakin ingin di batalkan suratnya?')">BATALKAN PESAN BALASAN</a>
                    <button class="btn btn-default" data-target="#tambah-lampiran" data-toggle="modal" type="button"><i class="fas fa-paperclip"></i> Tambah Lampiran</button>
                @endif
                @if ($surat->status == 'arsip' || Auth::user()->id == 192 || Auth::user()->id == 459 || Auth::user()->id == 462)
                    <a class="btn btn-success" href="{{ url('message/cetak_barcode/' . Crypt::encrypt($surat->surat_id)) }}" onclick="return confirm('Apakah anda yakin ingin mencetak barcode surat ini?')" target="_blank"><i class="fas fa-barcode"></i> Print QR Code</a>
                    <a class="btn btn-warning" href="{{ url('message/print_all/' . Crypt::encrypt($surat->surat_id)) }}" onclick="return confirm('Apakah anda yakin ingin mencetak surat ini?')" target="_blank"><i class="fas fa-barcode"></i> Print Surat</a>
                @endif
                @if (in_array(Auth::user()->id, explode(',', env('USER_ID_PENGADAAN'))))
                    <a class="btn btn-primary" href="http://192.168.0.14/pemesanan-umum/create?surat_id={{ $surat->surat_id }}" onclick="return confirm('Apakah anda yakin ingin membuat PO atas surat ini?')" target="_blank"><i class="fas fa-warehouse"></i> Buat PO</a>
                    {{-- <a class="btn btn-primary" href="http://127.0.0.1:9000/pemesanan-umum/create?surat_id={{ $surat->surat_id }}" onclick="return confirm('Apakah anda yakin ingin membuat PO atas surat ini?')" target="_blank"><i class="fas fa-warehouse"></i> Buat PO</a> --}}
                @endif
            </div>
            {{-- <button type="button" class="btn btn-default"><i class="far fa-trash-alt"></i> Delete</button> --}}
            {{-- <button type="button" class="btn btn-default"><i class="fas fa-print"></i> Print</button> --}}
        </div>
        <!-- /.card-footer -->
        <div class="modal fade" id="reply-surat">
            <div class="modal-dialog modal-lg">
                <form action="{{ url('message/reply') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Balas Surat</h4>
                            <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- <input type="hidden" name="bagian" value="{{ $surat->seri_bagian }}"> --}}
                            {{-- <input type="hidden" name="bagian" value="{{ $surat->akronim }}"> --}}
                            <input name="bagian" type="hidden" value="{{ $list_bagian->akronim }}">
                            <input name="surat_id" required type="hidden" value="{{ Crypt::encrypt($surat->surat_id) }}">
                            <input name="penerima_sebelumnya" required type="hidden" value="{{ Crypt::encrypt($surat->penerima_id) }}">
                            <div class="form-group">
                                <select class="form-control select2bs4" data-placeholder="Penerima" name="penerima_id" required style="width: 100%;">
                                    <option value=""></option>
                                    @foreach ($list_penerima as $penerima)
                                        @if ($penerima->id != Auth::user()->id)
                                            <option value="{{ $penerima->id }}">
                                                {{ $penerima->nama_hakakses . ' | ' . $penerima->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="select2-purple">
                                    <select class="select2" data-dropdown-css-class="select2-purple" data-placeholder="CC (Tidak harus dipilih)" multiple="multiple" name="cc[]" style="width: 100%;">
                                        @foreach ($list_penerima as $penerima)
                                            @if ($penerima->id != Auth::user()->id)
                                                <option value="{{ $penerima->id }}">{{ $penerima->nama_hakakses . ' | ' . $penerima->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" id="compose-textarea" name="pesan" required style="height: 300px">
                                </textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Upload Lampiran (opsional)</label>
                                <input accept=".jpg,.jpeg,.png,.svg,.zip,.xls,.docx,.vnd.openxmlformats-officedocument.wordprocessingml.document,.xlsx,.vnd.openxmlformats-officedocument.spreadsheetml.sheet,.pdf,.vnd.ms-excel" class="form-control" multiple name="file[]" placeholder="File" type="file">
                                {{-- <em class="text-danger">MOHON MAAF UNTUK SAAT INI FITUR UPLOAD FILE BELUM DAPAT DIGUNAKAN.</em> --}}
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
                            <button class="btn btn-primary" type="submit">Reply</button>
                        </div>
                    </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="arsip-surat">
            <div class="modal-dialog modal-lg">
                <form action="{{ url('message/reply') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Arsip Surat</h4>
                            <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- <input type="hidden" name="bagian" value="{{ $surat->seri_bagian }}"> --}}
                            {{-- <input type="hidden" name="bagian" value="{{ $surat->akronim }}"> --}}
                            <input name="bagian" type="hidden" value="{{ $list_bagian->akronim }}">
                            <input name="surat_id" required type="hidden" value="{{ Crypt::encrypt($surat->surat_id) }}">
                            <input name="arsipan" required type="hidden" value="arsip">
                            <input name="penerima_sebelumnya" required type="hidden" value="{{ Crypt::encrypt($surat->penerima_id) }}">
                            <div class="form-group">
                                <textarea class="form-control" id="compose-textarea" name="pesan" required style="height: 300px"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
                            <button class="btn btn-primary" type="submit">Arsipkan</button>
                        </div>
                    </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        @if ($datapenerima[$hitung - 1] == Auth::user()->id && empty($surat->status) && $surat->deleted_at == null)
            <div class="modal fade" id="tambah-lampiran">
                <div class="modal-dialog modal-lg">
                    <form action="{{ url('message/lampiran') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Balas Surat</h4>
                                <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input name="surat_id" required type="hidden" value="{{ Crypt::encrypt($surat->surat_id) }}">
                                <input name="surat_balasan_id" required type="hidden" value="{{ Crypt::encrypt($disposisi_terakhir) }}">
                                <div class="form-group">
                                    <label for="">Upload Lampiran (opsional)</label>
                                    <input accept=".jpg,.jpeg,.png,.svg,.zip,.xls,.docx,.vnd.openxmlformats-officedocument.wordprocessingml.document,.xlsx,.vnd.openxmlformats-officedocument.spreadsheetml.sheet,.pdf,.vnd.ms-excel" class="form-control" multiple name="file[]" placeholder="File" type="file">
                                    {{-- <em class="text-danger">MOHON MAAF UNTUK SAAT INI FITUR UPLOAD FILE BELUM DAPAT DIGUNAKAN.</em> --}}
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
                                <button class="btn btn-primary" type="submit">Reply</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        $('.select2').select2()
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            dropdownParent: $("#reply-surat")
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
    </script>
@endpush
