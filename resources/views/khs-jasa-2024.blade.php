<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KHS JASA - 2024</title>

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

            <!-- ============================= -->
            <!-- SIDEBAR -->
            <!-- ============================= -->

            <div class="sidebar">

                <details open>

                    <summary>📂 KHS JASA</summary>

                    <ul>

                        <li>
                            <a href="{{ url('/khs-jasa-2024') }}">
                                📁 2024
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/khs-jasa-2025') }}">
                                📁 2025
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/khs-jasa-2026') }}">
                                📁 2026
                            </a>
                        </li>

                    </ul>

                </details>

                <details>

                    <summary>📂 KHS PEMBERSIHAN</summary>

                    <ul>

                        <li>📁 2024</li>
                        <li>📁 2025</li>
                        <li>📁 2026</li>

                    </ul>

                </details>

                <details>

                    <summary>📂 REGRESASI</summary>

                    <ul>

                        <li>📁 2025</li>
                        <li>📁 2026</li>

                    </ul>

                </details>

            </div>

            <!-- ============================= -->
            <!-- TABEL -->
            <!-- ============================= -->

            <div class="table-area">

                <div class="table-title">

                    <h3>📋 KHS JASA - 2024</h3>

                </div>

                <table>

                    <thead>

                    <tr>

                        <th>No.</th>
                        <th>ID Pekerjaan</th>
                        <th>Nama Pekerjaan</th>
                        <th>Lokasi</th>
                        <th>Nilai Kontrak (Rp)</th>
                        <th>Progress (%)</th>
                        <th>Update Terakhir</th>

                    </tr>

                    </thead>

                    <tbody>

                    <tr>
                        <td>1</td>
                        <td>PJ-250201</td>
                        <td>Pekerjaan A</td>
                        <td>Jambi</td>
                        <td>3.000.000</td>
                        <td>10%</td>
                        <td>30/03/2024</td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>PJ-250202</td>
                        <td>Pekerjaan B</td>
                        <td>Jambi</td>
                        <td>4.500.000</td>
                        <td>25%</td>
                        <td>05/04/2024</td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td>PJ-250203</td>
                        <td>Pekerjaan C</td>
                        <td>Muaro Jambi</td>
                        <td>5.200.000</td>
                        <td>40%</td>
                        <td>10/04/2024</td>
                    </tr>

                    <tr>
                        <td>4</td>
                        <td>PJ-250204</td>
                        <td>Pekerjaan D</td>
                        <td>Batanghari</td>
                        <td>6.100.000</td>
                        <td>55%</td>
                        <td>16/04/2024</td>
                    </tr>

                    <tr>
                        <td>5</td>
                        <td>PJ-250205</td>
                        <td>Pekerjaan E</td>
                        <td>Tebo</td>
                        <td>4.750.000</td>
                        <td>65%</td>
                        <td>20/04/2024</td>
                    </tr>

                    <tr>
                        <td>6</td>
                        <td>PJ-250206</td>
                        <td>Pekerjaan F</td>
                        <td>Bungo</td>
                        <td>7.000.000</td>
                        <td>70%</td>
                        <td>24/04/2024</td>
                    </tr>

                    <tr>
                        <td>7</td>
                        <td>PJ-250207</td>
                        <td>Pekerjaan G</td>
                        <td>Sarolangun</td>
                        <td>5.800.000</td>
                        <td>80%</td>
                        <td>01/05/2024</td>
                    </tr>

                    <tr>
                        <td>8</td>
                        <td>PJ-250208</td>
                        <td>Pekerjaan H</td>
                        <td>Merangin</td>
                        <td>8.400.000</td>
                        <td>90%</td>
                        <td>05/05/2024</td>
                    </tr>

                    <tr>
                        <td>9</td>
                        <td>PJ-250209</td>
                        <td>Pekerjaan I</td>
                        <td>Kerinci</td>
                        <td>6.700.000</td>
                        <td>95%</td>
                        <td>08/05/2024</td>
                    </tr>

                    <tr>
                        <td>10</td>
                        <td>PJ-250210</td>
                        <td>Pekerjaan J</td>
                        <td>Sungai Penuh</td>
                        <td>9.200.000</td>
                        <td>100%</td>
                        <td>12/05/2024</td>
                    </tr>

                    </tbody>

                </table>

                <div class="bottom">

                    <small>
                        Menampilkan 1 - 10 dari 10 data
                    </small>

                    <div class="pagination">

                        <button>&laquo;</button>
                        <button>&lsaquo;</button>
                        <button>1</button>
                        <button>&rsaquo;</button>
                        <button>&raquo;</button>

                    </div>

                    <div class="search-box">

                        <input
                            type="text"
                            placeholder="Cari pekerjaan..."
                        >

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>