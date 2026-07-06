<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $jenis }} - {{ $tahun }}</title>

    @vite('resources/css/khs-jasa-2024.css')
</head>
<body>

<div class="container">

    <div class="main-box">

        <div class="header">

            <h3>Daftar Sheet</h3>

            <p>Pilih sheet untuk melihat detail data.</p>

        </div>

        <div class="content">

            <!-- Sidebar -->

            <div class="sidebar">

                <details open>

                    <summary>📂 KHS JASA</summary>

                    <ul>

                        @foreach([2024,2025,2026] as $th)

                        <li>

                            <a href="{{ route('detail.data',['jenis'=>'KHS JASA','tahun'=>$th]) }}">

                                📁 {{ $th }}

                            </a>

                        </li>

                        @endforeach

                    </ul>

                </details>

                <details>

                    <summary>📂 KHS PEMBERSIHAN</summary>

                    <ul>

                        @foreach([2024,2025,2026] as $th)

                        <li>

                            <a href="{{ route('detail.data',['jenis'=>'KHS PEMBERSIHAN','tahun'=>$th]) }}">

                                📁 {{ $th }}

                            </a>

                        </li>

                        @endforeach

                    </ul>

                </details>

                <details>

                    <summary>📂 REGRESASI</summary>

                    <ul>

                        @foreach([2025,2026] as $th)

                        <li>

                            <a href="{{ route('detail.data',['jenis'=>'REGRESASI','tahun'=>$th]) }}">

                                📁 {{ $th }}

                            </a>

                        </li>

                        @endforeach

                    </ul>

                </details>

            </div>

            <!-- Isi -->

            <div class="table-area">

                <div class="table-title">

                    <h3>

                        📋 {{ $jenis }} - {{ $tahun }}

                    </h3>

                </div>

                <table>

                    <thead>

                    <tr>

                        <th>No</th>
                        <th>Nama Sheet</th>
                        <th>ID Spreadsheet</th>
                        <th>Jumlah Sheet</th>
                        <th>Total Baris</th>
                        <th>File Excel</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($data as $item)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $item->nama_sheet }}</td>

                        <td>{{ $item->id_spreadsheet }}</td>

                        <td>{{ $item->jumlah_sheet }}</td>

                        <td>{{ $item->total_baris }}</td>

                        <td>

                            <a href="{{ asset($item->file_path) }}" target="_blank">

                                Lihat File

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6" style="text-align:center">

                            Belum ada data.

                        </td>

                    </tr>

                    @endforelse

                    </tbody>

                </table>

                <div class="bottom">

                    <small>

                        Total Data :
                        {{ $data->count() }}

                    </small>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>