<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ExcelFile;

class DashboardController extends Controller
{
    // ==========================
    // Dashboard User
    // ==========================
    public function userIndex()
    {
        if (!Auth::check()) {
            return redirect('/login')
                ->withErrors([
                    'email' => 'Anda harus login terlebih dahulu.'
                ]);
        }

        return view('dashboard');
    }

    // ==========================
    // Dashboard Admin
    // ==========================
    public function adminIndex()
    {
        if (!session()->has('admin')) {
            return redirect()->route('login.admin')
                ->withErrors([
                    'email' => 'Akses ditolak. Silakan login admin.'
                ]);
        }

        return view('dashboard-admin.dashboard-admin');
    }

    // ==========================
    // KHS JASA 2024
    // ==========================
    public function khsJasa2024()
    {
        $data = ExcelFile::where('jenis_data', 'KHS JASA')
            ->where('tahun', 2024)
            ->get();

        return view('khs-jasa-2024', compact('data'));
    }

    // ==========================
    // KHS JASA 2025
    // ==========================
    public function khsJasa2025()
    {
        $data = ExcelFile::where('jenis_data', 'KHS JASA')
            ->where('tahun', 2025)
            ->get();

        return view('khs-jasa-2025', compact('data'));
    }

    // ==========================
    // KHS JASA 2026
    // ==========================
    public function khsJasa2026()
    {
        $data = ExcelFile::where('jenis_data', 'KHS JASA')
            ->where('tahun', 2026)
            ->get();

        return view('khs-jasa-2026', compact('data'));
    }

    // ==========================
    // PEMBERSIHAN 2024
    // ==========================
    public function pemb2024()
    {
        $data = ExcelFile::where('jenis_data', 'KHS PEMBERSIHAN')
            ->where('tahun', 2024)
            ->get();

        return view('khs-pembersihan-2024', compact('data'));
    }

    public function pemb2025()
    {
        $data = ExcelFile::where('jenis_data', 'KHS PEMBERSIHAN')
            ->where('tahun', 2025)
            ->get();

        return view('khs-pembersihan-2025', compact('data'));
    }

    public function pemb2026()
    {
        $data = ExcelFile::where('jenis_data', 'KHS PEMBERSIHAN')
            ->where('tahun', 2026)
            ->get();

        return view('khs-pembersihan-2026', compact('data'));
    }

    // ==========================
    // REGRESASI
    // ==========================
    public function regresasi2025()
    {
        $data = ExcelFile::where('jenis_data', 'REGRESASI')
            ->where('tahun', 2025)
            ->get();

        return view('regresasi-2025', compact('data'));
    }

    public function regresasi2026()
    {
        $data = ExcelFile::where('jenis_data', 'REGRESASI')
            ->where('tahun', 2026)
            ->get();

        return view('regresasi-2026', compact('data'));
    }
}