@extends('layouts.tamplate')

@section('content')
    <form action="{{ url('message/update') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Ubah Surat</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                {{-- <div class="form-group">
                    <div class="select2-purple">
                        <select class="select2" required name="penerima_id[]" multiple="multiple" data-placeholder="Penerima"
                            data-dropdown-css-class="select2-purple" style="width: 100%;">
                            @foreach ($list_penerima as $penerima)
                                @if($penerima->id != Auth::user()->id)
                                    <option value="{{ $penerima->id }}">{{ $penerima->nama_hakakses ." | ".$penerima->name }}</option>
                                @endif
                            @endforeach

                        </select>
                    </div>
                </div> --}}
                <input type="hidden" name="bagian" value="{{ $list_bagian->seri_bagian }}">
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
                    <div class="select2-purple">
                        <select class="select2" name="cc[]" multiple="multiple" data-placeholder="CC (Tidak harus dipilih)"
                            data-dropdown-css-class="select2-purple" style="width: 100%;"> 
                            @foreach ($list_penerima as $penerima)
                                @if($penerima->id != Auth::user()->id)
                                    <option value="{{ $penerima->id }}">{{ $penerima->nama_hakakses ." | ".$penerima->name }}</option>
                                @endif
                            @endforeach

                        </select>
                    </div>
                </div>
                <input class="form-control" type="hidden" required name="surat_id" value="{{ $surat->surat_id }}">
                <div class="form-group">
                    <input class="form-control" type="text" required name="judul" value="{{ $surat->judul_surat }}" placeholder="Judul Surat">
                </div>
                {{-- <div class="form-group">
                    <input class="form-control" type="text" value="{{ $surat->no_surat }}" required name="no_surat" placeholder="Nomor Surat">
                </div> --}}
                <div class="form-group">
                    <textarea id="compose-textarea" required name="pesan" class="form-control" style="height: 300px">
                        {!! $surat->isi_surat !!}
                    </textarea>
                </div>
                <div class="form-group">
                    <label for="">Upload Lampiran (opsional)</label>
                    <input class="form-control" type="file" multiple name="file[]" placeholder="File">
                    {{-- <em class="text-danger">MOHON MAAF UNTUK SAAT INI FITUR UPLOAD FILE BELUM DAPAT DIGUNAKAN.</em> --}}
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" value="draft" name="draft" class="btn btn-default"><i
                            class="fas fa-pencil-alt"></i> Draft</button>
                    <button type="submit" value="simpan" name="simpan" class="btn btn-primary"><i
                            class="far fa-envelope"></i> Send</button>
                </div>
                <button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Discard</button>
            </div>
            
            <!-- /.card-footer -->
        </div>
    </form>
    {{-- <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">INFORMASI</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2 class="text-danger">NEW UPDATE!!!</h2>
                    <ul>
                        <li></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('scripts')
    <script>
        // $('#editModal').modal('show');
        $('.select2').select2()
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
        Dropzone.options.dropzoneFrom = {
            autoProcessQueue: false,
            acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
            init: function() {
                var submitButton = document.querySelector('#submit-all');
                myDropzone = this;
                submitButton.addEventListener("click", function() {
                    myDropzone.processQueue();
                });
                this.on("complete", function() {
                    if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
                        var _this = this;
                        _this.removeAllFiles();
                    }
                    list_image();
                });
            },
        };

        list_image();

        function list_image() {
            $.ajax({
                url: "upload.php",
                success: function(data) {
                    $('#preview').html(data);
                }
            });
        }

        $(document).on('click', '.remove_image', function() {
            var name = $(this).attr('id');
            $.ajax({
                url: "upload.php",
                method: "POST",
                data: {
                    name: name
                },
                success: function(data) {
                    list_image();
                }
            })
        });
    </script>
@endpush
