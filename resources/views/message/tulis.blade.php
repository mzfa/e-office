@extends('layouts.tamplate')

@section('content')
    <form action="{{ url('message/store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Buat Surat Baru</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="form-group">
                    <select class="form-control select2bs4" data-dropdown-css-class="select2-danger" data-placeholder="Penerima" style="width: 100%;" name="penerima_id" required>
                        @foreach ($list_penerima as $penerima)
                            @if($penerima->id != Auth::user()->id)
                                <option value="{{ $penerima->id }}">{{ $penerima->nama_hakakses ." | ".$penerima->name }}</option>
                            @endif
                        @endforeach
                    </select>
                  </div>
                  <input type="hidden" name="bagian" value="{{ $list_bagian->akronim }}">
                {{-- <div class="form-group">
                    <select class="form-control select2bs4" data-dropdown-css-class="select2-danger" data-placeholder="Penerima" style="width: 100%;" name="profesi" required>
                        @foreach ($list_profesi as $profesi)
                            <option value="{{ $profesi->nama_profesi }}">{{ $profesi->nama_profesi }}</option>
                        @endforeach
                    </select>
                  </div> --}}
                {{-- <div class="form-group">
                    <div class="select2-purple">
                        <select class="select2" name="penerima_id[]" multiple="multiple" data-placeholder="Penerima"
                            data-dropdown-css-class="select2-purple" style="width: 100%;" required>
                            @foreach ($list_penerima as $penerima)
                                @if($penerima->id != Auth::user()->id)
                                    <option value="{{ $penerima->id }}">{{ $penerima->nama_hakakses ." | ".$penerima->name }}</option>
                                @endif
                            @endforeach

                        </select>
                    </div>
                </div> --}}
                <div class="form-group">
                    <input class="form-control" type="text" name="judul" required placeholder="Judul Surat">
                </div>
                {{-- <div class="form-group">
                    <input class="form-control" type="text" name="no_surat" required placeholder="Nomor Surat">
                </div> --}}
                <div class="form-group">
                    <textarea id="compose-textarea" rows="5" name="pesan" required class="form-control">
                        
                    </textarea>
                </div>

                <div class="form-group">
                    <label for="">Upload Lampiran (opsional)</label>
                    <input class="form-control" type="file" multiple name="file[]" placeholder="File">
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
@endsection

@push('scripts')
    <script>
        $('.select2').select2()
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
        // Dropzone.options.dropzoneFrom = {
        //     autoProcessQueue: false,
        //     acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
        //     init: function() {
        //         var submitButton = document.querySelector('#submit-all');
        //         myDropzone = this;
        //         submitButton.addEventListener("click", function() {
        //             myDropzone.processQueue();
        //         });
        //         this.on("complete", function() {
        //             if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
        //                 var _this = this;
        //                 _this.removeAllFiles();
        //             }
        //             list_image();
        //         });
        //     },
        // };

        // list_image();

        // function list_image() {
        //     $.ajax({
        //         url: "upload.php",
        //         success: function(data) {
        //             $('#preview').html(data);
        //         }
        //     });
        // }

        // $(document).on('click', '.remove_image', function() {
        //     var name = $(this).attr('id');
        //     $.ajax({
        //         url: "upload.php",
        //         method: "POST",
        //         data: {
        //             name: name
        //         },
        //         success: function(data) {
        //             list_image();
        //         }
        //     })
        // });
    </script>
@endpush
