@extends('layouts.tamplate')

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Read Mail</h3>

        </div>
        <!-- /.card-header -->
        <div class="card-body p-0">
            <div class="mailbox-read-info">
                <h5>{{ $surat->judul_surat }}</h5>
                <h6 class="mt-2">From: <b>{{ $surat->nama_pegawai }}</b>
                    <span class="mailbox-read-time float-right">{{ date('d-M-Y H:i:s', strtotime($surat->created_at)) }}</span>
                </h6>
                <h6>To : <b>
                    @php
                        $penerima_id = explode('|',$surat->penerima_id);
                        // dump($penerima_id);
                        for ( $i = 0; $i < count( $penerima_id ); $i++ ) {
                            $id = $penerima_id[$i];
                            if($penerima_id[$i] > 0){
                                $user = DB::table('users')->leftJoin('pegawai', 'users.pegawai_id', '=', 'pegawai.pegawai_id')->where(['users.id' => $id])->first();
                                echo $user->nama_pegawai . " , ";
                            }
                        }
                    @endphp
                    </b>
                </h6>
            </div>
            <!-- /.mailbox-read-info -->
            {{-- <div class="mailbox-controls with-border text-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm" data-container="body" title="Delete">
                        <i class="far fa-trash-alt"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-container="body" title="Reply">
                        <i class="fas fa-reply"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-container="body" title="Forward">
                        <i class="fas fa-share"></i>
                    </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm" title="Print">
                    <i class="fas fa-print"></i>
                </button>
            </div> --}}
            <!-- /.mailbox-controls -->
            <div class="mailbox-read-message">
                {!! $surat->isi_surat !!}
            </div>
            @php
            $datapenerima = explode('|',$surat->penerima_id);
            $hitung = count($datapenerima)-2;
            // dump($datapenerima[$hitung]);
            @endphp
            @foreach($surat_balasan as $balas)
                <hr>
                <div class="mailbox-read-message text-right">
                    <u><h5>Dijawab : {{ $balas->nama_pegawai }}</h5></u>
                    {{-- <u><h5>Diteruskan : {{ $balas->nama_pegawai }}</h5></u> --}}
                    <em>{{ \Carbon\Carbon::parse($balas->created_at)->diffForHumans() }}</em><br><br>
                    {!! $balas->isi_balasan !!}
                </div>
            @endforeach
            <!-- /.mailbox-read-message -->
        </div>
        <!-- /.card-body -->
        {{-- <div class="card-footer bg-white">
            <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                <li>
                    <span class="mailbox-attachment-icon"><i class="far fa-file-pdf"></i></span>

                    <div class="mailbox-attachment-info">
                        <a href="#" class="mailbox-attachment-name"><i class="fas fa-paperclip"></i>
                            Sep2014-report.pdf</a>
                        <span class="mailbox-attachment-size clearfix mt-1">
                            <span>1,245 KB</span>
                            <a href="#" class="btn btn-default btn-sm float-right"><i
                                    class="fas fa-cloud-download-alt"></i></a>
                        </span>
                    </div>
                </li>
                <li>
                    <span class="mailbox-attachment-icon"><i class="far fa-file-word"></i></span>

                    <div class="mailbox-attachment-info">
                        <a href="#" class="mailbox-attachment-name"><i class="fas fa-paperclip"></i> App
                            Description.docx</a>
                        <span class="mailbox-attachment-size clearfix mt-1">
                            <span>1,245 KB</span>
                            <a href="#" class="btn btn-default btn-sm float-right"><i
                                    class="fas fa-cloud-download-alt"></i></a>
                        </span>
                    </div>
                </li>
                <li>
                    <span class="mailbox-attachment-icon has-img"><img src="../../dist/img/photo1.png"
                            alt="Attachment"></span>

                    <div class="mailbox-attachment-info">
                        <a href="#" class="mailbox-attachment-name"><i class="fas fa-camera"></i> photo1.png</a>
                        <span class="mailbox-attachment-size clearfix mt-1">
                            <span>2.67 MB</span>
                            <a href="#" class="btn btn-default btn-sm float-right"><i
                                    class="fas fa-cloud-download-alt"></i></a>
                        </span>
                    </div>
                </li>
                <li>
                    <span class="mailbox-attachment-icon has-img"><img src="../../dist/img/photo2.png"
                            alt="Attachment"></span>

                    <div class="mailbox-attachment-info">
                        <a href="#" class="mailbox-attachment-name"><i class="fas fa-camera"></i> photo2.png</a>
                        <span class="mailbox-attachment-size clearfix mt-1">
                            <span>1.9 MB</span>
                            <a href="#" class="btn btn-default btn-sm float-right"><i
                                    class="fas fa-cloud-download-alt"></i></a>
                        </span>
                    </div>
                </li>
            </ul>
        </div> --}}
        <!-- /.card-footer -->
        <div class="card-footer">
            <div class="float-right">
                {{-- <button type="button" class="btn btn-default"><i class="fas fa-reply"></i> Reply</button> --}}
                @if($datapenerima[$hitung] == Auth::user()->id)
                    <button type="button" data-toggle="modal" data-target="#reply-surat" class="btn btn-default"><i class="fas fa-share"></i> Balas</button>
                @endif
            </div>
            {{-- <button type="button" class="btn btn-default"><i class="far fa-trash-alt"></i> Delete</button> --}}
            <button type="button" class="btn btn-default"><i class="fas fa-print"></i> Print</button>
        </div>
        <!-- /.card-footer -->
        <div class="modal fade" id="reply-surat">
            <div class="modal-dialog modal-lg">
                <form action="{{ url('message/reply') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Balas Surat</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" required name="surat_id" value="{{ Crypt::encrypt($surat->surat_id) }}">
                            <input type="hidden" required name="penerima_sebelumnya" value="{{ Crypt::encrypt($surat->penerima_id) }}">
                            {{-- <div class="form-group">
                                <div class="select2-purple">
                                    <select class="select2" name="penerima_id[]" multiple="multiple" data-placeholder="Penerima"
                                        data-dropdown-css-class="select2-purple" style="width: 100%;" required>
                                        @foreach ($list_penerima as $penerima)
                                            @if($penerima->id != Auth::user()->id && $penerima->id != $surat->user_id && $penerima->id != 0)
                                                @php $id_penerima = explode('|',$surat->penerima_id); @endphp
                                                @if(array_search($penerima->id,$id_penerima))

                                                @else
                                                    <option value="{{ $penerima->id }}" >{{ $penerima->nama_hakakses ." | ".$penerima->name }}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="form-group">
                                <select class="form-control select2bs4" data-dropdown-css-class="select2-danger" data-placeholder="Penerima" style="width: 100%;" name="penerima_id" required>
                                    @foreach ($list_penerima as $penerima)
                                        @if($penerima->id != Auth::user()->id)
                                            <option value="{{ $penerima->id }}">{{ $penerima->nama_hakakses ." | ".$penerima->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                              </div>
                            <div class="form-group">
                                <textarea id="compose-textarea" name="pesan" class="form-control" style="height: 300px">
                                </textarea>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Reply</button>
                        </div>
                    </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>

    
@endsection

@push('scripts')
    <script>
        $('.select2').select2()
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
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
