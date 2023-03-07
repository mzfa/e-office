@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Surat Pembelian Barang &nbsp;
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-lg">
                                Tambah
                            </button>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="example1" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Dibuat</th>
                                        <th>Nama File</th>
                                        <th>Judul Surat</th>
                                        <th>No Surat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $item->created_at }}</td>
                                            <td><a href="{{ url('document/spb/'.$item->nama_file_spb) }}" target="_blank"> {{ $item->nama_file_spb }}</a></td>
                                            <td>{{ $item->judul_surat }}</td>
                                            <td><a href="{{ url('cek_surat_dong/'.base64_encode($item->surat_id)) }}" target="_blank">{{ $item->no_surat }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-lg">
            <div class="modal-dialog modal-lg">
                <form action="{{ url('spb/store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Upload SPB</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select class="form-control select2bs4" data-dropdown-css-class="select2-danger" data-placeholder="surat" style="width: 100%;" name="surat_id" required>
                                    @foreach ($list_surat as $surat)
                                        <option value="{{ $surat->surat_id }}">{{ $surat->no_surat ." | ".$surat->judul_surat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Upload SPB</label>
                                <input class="form-control" type="file" name="file" placeholder="File" required>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
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
        function akses(id) {
            $.ajax({
                type: 'get',
                url: "{{ url('user/edit') }}/" + id,
                // data:{'id':id}, 
                success: function(tampil) {

                    // console.log(tampil); 
                    $('#tampildata').html(tampil);
                    $('#editModal').modal('show');
                }
            })
        }
    </script>
@endpush
