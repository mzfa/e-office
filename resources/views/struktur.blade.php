@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Struktur &nbsp;
                            <a tooltip="Sync Data Struktur" href="{{ url('struktur/sync') }}" id="create_record" class="btn btn-danger text-white shadow-sm">
                                <i class="bi bi-sync"></i> Sync
                            </a>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Nama Struktur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no=1 @endphp
                                    @foreach ($struktur as $item)
                                        <tr class="bg-info">
                                            <td>
                                                <h5 class="text-white">{{ strtoupper($item['nama_struktur']) }}</h5>
                                                @if ($item['parent_id'] == 0)
                                                @else
                                                    <h5 class="text-primary">
                                                        {{ strtoupper($item['nama_struktur']) }}</h5>
                                                @endif
                                            </td>
                                        </tr>
                                        @foreach($item['substruktur'] as $substruktur)
                                        <tr class="bg-primary">
                                            <td>
                                                <h5 class="text-white">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ strtoupper($substruktur['nama_struktur']) }}</h5>
                                            </td>
                                        </tr>
                                            @foreach($substruktur['substruktur1'] as $substruktur1)
                                            <tr class="bg-success">
                                                <td>
                                                    <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ strtoupper($substruktur1['nama_struktur']) }}</h5>
                                                </td>
                                            </tr>
                                                @foreach($substruktur1['substruktur2'] as $substruktur2)
                                                <tr class="bg-secondary">
                                                    <td>
                                                        <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ strtoupper($substruktur2['nama_struktur']) }}</h5>
                                                    </td>
                                                </tr>
                                                    @foreach($substruktur2['substruktur3'] as $substruktur3)
                                                    <tr>
                                                        <td>
                                                            <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ strtoupper($substruktur3['nama_struktur']) }}</h5>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
