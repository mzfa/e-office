@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Surat yang sudah di arsipkan &nbsp;</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <select class="form-control select2bs4" data-dropdown-css-class="select2-danger" data-placeholder="Penerima" style="width: 100%;" name="penerima_id" id="penerima_id" required>
                                @foreach ($struktur as $item)
                                    {{-- @if($item->struktur_id != Auth::user()->id) --}}
                                    <option value="{{ $item->akronim }}">{{ Str::upper($item->nama_struktur) ." | ".$item->akronim }}</option>
                                    {{-- @endif --}}
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block" onclick="arsip_detail()">Submit</button>
                        </div>
                        <div id="message-content"></div>
                    </div>
                </div>
            </div>
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
        function arsip_detail() {
            var id = $('#penerima_id').val();
            $.ajax({
                type: 'get',
                url: "{{ url('monitoring/arsip_detail') }}/" + id,
                // data:{'id':id}, 
                beforeSend: function() {
                    var url = "{{ url('assets/dist/img/Loading_2.gif') }}";
                    $('#message-content').html('<center><img src="'+url+'"></center>');
                },
                success: function(tampil) {
                    $('#message-content').html(tampil);
                }
            })
        }
    </script>
@endpush
