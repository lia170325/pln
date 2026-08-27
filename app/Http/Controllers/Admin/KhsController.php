<?php

namespace App\Http\Controllers\Admin; // <-- INI YANG DITAMBAHKAN (\Admin)

use App\Http\Controllers\Controller; // <-- INI JUGA WAJIB DITAMBAHKAN
use Illuminate\Http\Request;
use App\Models\Khs;
use Illuminate\Support\Facades\Storage;

class KhsController extends Controller
{
    // 1. Menampilkan Form Tambah Data
    public function create()
    {
        return view('dashboard-admin.khs-create');
    }

    // 2. Memproses Simpan Data
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'arcgis_link' => 'nullable|url',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kontrak_pdf' => 'nullable|mimes:pdf|max:5000',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('uploads/gambar', 'public');
        }

        if ($request->hasFile('kontrak_pdf')) {
            $data['kontrak_pdf'] = $request->file('kontrak_pdf')->store('uploads/pdf', 'public');
        }

        Khs::create($data);

        return redirect()->route('admin.dashboard')->with('success', 'Data KHS Berhasil Ditambahkan!');
    }

    // 3. Menampilkan Form Edit / Upload File
    public function edit($id)
    {
        $khs = Khs::findOrFail($id);
        return view('dashboard-admin.khs-edit', compact('khs'));
    }

    // 4. Memproses Update Data & File
    public function update(Request $request, $id)
    {
        $khs = Khs::findOrFail($id);

        $data = $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'arcgis_link' => 'nullable|url',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kontrak_pdf' => 'nullable|mimes:pdf|max:5000',
        ]);

        if ($request->hasFile('gambar')) {
            if ($khs->gambar) {
                Storage::disk('public')->delete($khs->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('uploads/gambar', 'public');
        }

        if ($request->hasFile('kontrak_pdf')) {
            if ($khs->kontrak_pdf) {
                Storage::disk('public')->delete($khs->kontrak_pdf);
            }
            $data['kontrak_pdf'] = $request->file('kontrak_pdf')->store('uploads/pdf', 'public');
        }

        $khs->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Data KHS Berhasil Diperbarui!');
    }

    // 5. Menghapus Data
    public function destroy($id)
    {
        $khs = Khs::findOrFail($id);

        if ($khs->gambar) {
            Storage::disk('public')->delete($khs->gambar);
        }
        if ($khs->kontrak_pdf) {
            Storage::disk('public')->delete($khs->kontrak_pdf);
        }

        $khs->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Data KHS Berhasil Dihapus!');
    }
}