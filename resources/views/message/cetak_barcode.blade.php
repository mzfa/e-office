<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode Riwayat Surat</title>
</head>
<body>
    
    <center>
        <h1>QR Code</h1>
        <p>{{ $surat->judul_surat }} Diajukan oleh {{ $surat->nama_pegawai }}</p>
    
        {!! QrCode::size(300)->generate(url('/cek_surat_dong/').base64_encode($surat->surat_id) ) !!}
        <br>
        <em>ID : {{ base64_encode($surat->surat_id) }}</em>
        <br>
        <p>NOMOR SURAT</p>
        <h2>{{ $surat->no_surat }}</h2>
    </center>


<script>
    window.print();
</script>
</body>
</html>