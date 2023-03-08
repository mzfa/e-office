@extends('layouts.tamplate')

@section('content')
    <div id="message-content">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Draft</h3>

                <div class="card-tools">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search Mail">
                        <div class="input-group-append">
                            <div class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
               
                <div class="table-responsive mailbox-messages">
                    <table class="table table-hover table-striped">
                        <tbody>
                            <div id="tableData">
                                @foreach($list_surat as $surat)
                                <tr>
                                    {{-- <td>
                                        <div class="icheck-primary">
                                            <input type="checkbox" value="" id="check1">
                                            <label for="check1"></label>
                                        </div>
                                    </td> --}}
                                    <td class="mailbox-name"><a onclick="editSurat('{{ $surat->surat_id }}')">{{ $surat->judul_surat }}</a></td>
                                    {{-- <td class="mailbox-subject"><b>{{ $surat->judul_surat }}</b></td> --}}
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
        function editSurat(id) {
            $.ajax({
                type: 'get',
                url: "{{ url('message/edit') }}/"+id,
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
