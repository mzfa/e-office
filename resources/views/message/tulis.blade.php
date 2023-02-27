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
                    <div class="select2-purple">
                        <select class="select2" name="penerima_id[]" multiple="multiple" data-placeholder="Penerima"
                            data-dropdown-css-class="select2-purple" style="width: 100%;">
                            @foreach ($list_penerima as $penerima)
                                @if($penerima->id != Auth::user()->id)
                                    <option value="{{ $penerima->id }}">{{ $penerima->name }}</option>
                                @endif
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" name="judul" placeholder="Judul Surat">
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" name="no_surat" placeholder="Nomor Surat">
                </div>
                <div class="form-group">
                    <input class="form-control" type="file" multiple name="file" placeholder="File">
                </div>
                <div class="form-group">
                    <textarea id="compose-textarea" name="pesan" class="form-control" style="height: 300px">
                        <center>
                            <u><h3>NOTA DINAS</h3></u>
                            <h6>No. </h6>
                        </center>
                        <br>
                        <p>Isi Surat</p>
                    </textarea>
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
