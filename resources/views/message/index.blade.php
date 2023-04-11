@extends('layouts.app')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <button onclick="tulis()" class="btn btn-primary btn-block mb-3">Buat Surat</button>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Folders</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item" onclick="inboxOpen()">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-inbox"></i> Surat Masuk
                                    <span class="badge bg-danger float-right">{{ $inbox }}</span>
                                </a>
                            </li>
                            <li onclick="sentOpen()" class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-envelope"></i> Surat Terkirim
                                    <span class="badge bg-primary float-right">{{ $terkirim }}</span>
                                </a>
                            </li>
                            <li onclick="draftOpen()" class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-file-alt"></i> Draf
                                    <span class="badge bg-primary float-right">{{ $draft }}</span>
                                </a>
                            </li>
                            <li onclick="arsipOpen()" class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-folder-open"></i> Arsip
                                    <span class="badge bg-primary float-right">{{ $arsip }}</span>
                                </a>
                            </li>
                            <li onclick="terusanOpen()" class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-folder-open"></i> Terusan
                                    <span class="badge bg-primary float-right">{{ $terusan }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <!-- Preloader -->
                {{-- <span class="loader"></span> --}}
                
                <div id="message-content">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Inbox</h3>

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
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm">
                                        <i class="fas fa-share"></i>
                                    </button>
                                </div>
                                <!-- /.btn-group -->
                                <button type="button" class="btn btn-default btn-sm">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <div class="float-right">
                                    1-50/200
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <!-- /.btn-group -->
                                </div>
                                <!-- /.float-right -->
                            </div>
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-hover table-striped">
                                    
                                </table>
                                <!-- /.table -->
                            </div>
                            <!-- /.mail-box-messages -->
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer p-0">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button type="button" class="btn btn-default btn-sm checkbox-toggle">
                                    <i class="far fa-square"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm">
                                        <i class="fas fa-share"></i>
                                    </button>
                                </div>
                                <!-- /.btn-group -->
                                <button type="button" class="btn btn-default btn-sm">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <div class="float-right">
                                    1-50/200
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <!-- /.btn-group -->
                                </div>
                                <!-- /.float-right -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection

@push('scripts')
    <script>
        var pencarian = "";
        $.ajax({
            type: 'get',
            url: "{{ url('message/inbox') }}/",
            data:{'pencarian':pencarian}, 
            beforeSend: function() {
                var url = "{{ url('assets/dist/img/Loading_2.gif') }}";
                $('#message-content').html('<center><img src="'+url+'"></center>');
            },
            success: function(tampil) {
                $('#message-content').html(tampil);
                // $("#loading-image").hide();
            }
        })
        function edit(id) {
            $.ajax({
                type: 'get',
                url: "{{ url('menu/edit') }}/" + id,
                // data:{'id':id}, 
                success: function(tampil) {

                    // console.log(tampil); 
                    $('#tampildata').html(tampil);
                    $('#editModal').modal('show');
                }
            })
        }

        function tulis() {
            $.ajax({
                type: 'get',
                url: "{{ url('message/tulis') }}/",
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
        function inboxOpen() {
            var pencarian = "";
            $.ajax({
                type: 'get',
                url: "{{ url('message/inbox') }}/",
                data:{'pencarian':pencarian}, 
                beforeSend: function() {
                    var url = "{{ url('assets/dist/img/Loading_2.gif') }}";
                    $('#message-content').html('<center><img src="'+url+'"></center>');
                },
                success: function(tampil) {
                    $('#message-content').html(tampil);
                    // $("#loading-image").hide();
                }
            })
        }
        function sentOpen() {
            $.ajax({
                type: 'get',
                url: "{{ url('message/sent') }}/",
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
        function arsipOpen() {
            $.ajax({
                type: 'get',
                url: "{{ url('message/arsipOpen') }}/",
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
        function draftOpen() {
            $.ajax({
                type: 'get',
                url: "{{ url('message/draft') }}/",
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
        function terusanOpen() {
            // var url = "{{ url('assets/dist/img/cs.jpg') }}";
            // $('#message-content').html('<center><img src="'+url+'" width="100%"></center>');
            $.ajax({
                type: 'get',
                url: "{{ url('message/terusan') }}/",
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

        function tambahsubmenu(id) {
            $('#parent_id').val(id);
            $('#subMenuModal').modal('show');
        }
    </script>
@endpush
