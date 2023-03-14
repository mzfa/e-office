@extends('layouts.app')

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-12">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Selamat Datang, {{ $user_akses->name }}</h3>

                <p>Sebagai, {{ $user_akses->nama_hakakses }} Pada bagian {{ $user_akses->nama_struktur }}. Pada Aplikasi E-Office Rumah Sakit Umum Pekerja</p>
            </div>
            <div class="icon">
                <i class="ion ion-hospital-o"></i>
            </div>
            <a href="{{ url('message') }}" class="small-box-footer"><i class="fas fa-envelope"></i> &nbsp; Lanjut Ke Surat </a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->
<!-- Main row -->

@endsection