<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Update Data</title>

    @vite('resources/css/update-data.css')

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">

            <img src="{{ asset('images/logo-pln.png') }}" alt="PLN">

        </div>

        <h4>MENU ADMIN</h4>

        <ul>

            <li>

                <span class="material-icons">add_circle_outline</span>

                Input Data

            </li>

            <li class="active">

                <span class="material-icons">check_circle_outline</span>

                Update Data

            </li>

        </ul>

    </aside>

    <!-- CONTENT -->

    <main class="content">

        <div class="topbar">

            <div>

                <h2>Update Data</h2>

                <small>Kelola data spreadsheet</small>

            </div>

            <div class="admin">

                <span class="material-icons">account_circle</span>

                <small>Admin</small>

            </div>

        </div>

        <div class="card">

            <div class="card-title">

                <span class="material-icons">
                    task_alt
                </span>

                <div>

                    <h3>Update Data</h3>

                    <small>Perbarui Data yang Sudah Ada</small>

                </div>

            </div>

            <table>

                <thead>

                <tr>

                    <th>Nama Sheet</th>

                    <th>Kategori</th>

                    <th>Tahun</th>

                    <th>Jumlah Data</th>

                    <th>Terakhir Input</th>

                    <th>Aksi</th>

                </tr>

                </thead>

                <tbody>

                <tr>

                    <td>Detail KR</td>

                    <td>KHS Jasa</td>

                    <td>2026</td>

                    <td>10</td>

                    <td>30 Mei 2026 <br>08.00 WIB</td>

                    <td>

                        <a href="#" class="btn-update">

                            Update Data

                        </a>

                    </td>

                </tr>

                <tr>

                    <td>Detail KR</td>

                    <td>KHS Pembesian</td>

                    <td>2026</td>

                    <td>20</td>

                    <td>30 Mei 2026 <br>08.00 WIB</td>

                    <td>

                        <a href="#" class="btn-update">

                            Update Data

                        </a>

                    </td>

                </tr>

                <tr>

                    <td>Detail KR</td>

                    <td>KHS Jasa</td>

                    <td>2026</td>

                    <td>40</td>

                    <td>30 Mei 2026 <br>08.00 WIB</td>

                    <td>

                        <a href="#" class="btn-update">

                            Update Data

                        </a>

                    </td>

                </tr>

                </tbody>

            </table>

        </div>

    </main>

</div>

</body>
</html>