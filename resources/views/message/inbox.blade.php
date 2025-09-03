@extends('layouts.tamplate')

@section('content')
    <div id="message-content">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Inbox</h3>

                <div class="card-tools">
                    <div class="input-group input-group-sm">
                        <input class="form-control" id="pencarian" onkeyup="cariData(this)" placeholder="Search Mail" type="text">
                        <div class="input-group-append">
                            {{-- <div class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="table-responsive mailbox-messages">
                    <table class="table table-hover table-striped" id="example2">
                        <tbody>
                            <div id="tableData">
                                @foreach ($list_surat as $surat)
                                    @php
                                        $pecah = explode('|', $surat->penerima_id);
                                        $total_array = count($pecah) - 2;
                                        $id_user = $pecah[$total_array];
                                    @endphp
                                    <tr>
                                        {{-- <td>
                                        <div class="icheck-primary">
                                            <input type="checkbox" value="" id="check1">
                                            <label for="check1"></label>
                                        </div>
                                    </td> --}}
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="mailbox-name" onclick="lihatSurat('{{ $surat->surat_id }}')"><a href="#">{{ $surat->no_surat }}</a>
                                            @if ($id_user == Auth::user()->id && $surat->deleted_by == null)
                                                <sup class="badge badge-warning">Belum Dibalas</sup>
                                            @elseif($surat->deleted_by != null)
                                                <sup class="badge badge-danger">Surat dihapus pemilik</sup>
                                            @endif
                                        </td>
                                        <td class="mailbox-subject"><b>{{ $surat->judul_surat }}</b></td>
                                        <td class="mailbox-attachment">
                                            @php

                                            @endphp
                                        </td>
                                        <td class="mailbox-date">{{ \Carbon\Carbon::parse($surat->created_at)->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </div>
                        </tbody>
                    </table>
                    <!-- /.table -->
                </div>
                <!-- /.mail-box-messages -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function cariData(text) {
            // var pencarian = document.getElementById('pencarian').value;
            if (event.keyCode === 13) {
                console.log(text.value);
                var pencarian = text.value;
                // $.ajax({
                //     type: 'get',
                //     url: "{{ url('message/pencarian') }}/inbox/" + text.value,
                //     // data:{'id':id}, 
                //     success: function(tampil) {
                //         $('#tableData').html(tampil);
                //     }
                // })
                $.ajax({
                    type: 'get',
                    url: "{{ url('message/inbox') }}/",
                    data: {
                        'pencarian': pencarian
                    },
                    beforeSend: function() {
                        var url = "{{ url('assets/dist/img/Loading_2.gif') }}";
                        $('#message-content').html('<center><img src="' + url + '"></center>');
                    },
                    success: function(tampil) {
                        $('#message-content').html(tampil);
                        // $("#loading-image").hide();
                    }
                })
            }
        }

        function lihatSurat(id) {
            $.ajax({
                type: 'get',
                url: "{{ url('message/read') }}/" + id,
                // data:{'id':id}, 
                beforeSend: function() {
                    var url = "{{ url('assets/dist/img/Loading_2.gif') }}";
                    $('#message-content').html('<center><img src="' + url + '"></center>');
                },
                success: function(tampil) {
                    $('#message-content').html(tampil);
                }
            })
        }
    </script>
@endpush
