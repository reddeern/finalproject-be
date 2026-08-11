<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\PelangganData;
use App\Models\Penyewaan;
use App\Models\PenyewaanDetail;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori
        $kategoriKamera = Kategori::create(['kategori_nama' => 'Kamera']);
        $kategoriLensa  = Kategori::create(['kategori_nama' => 'Lensa']);
        $kategoriDrone  = Kategori::create(['kategori_nama' => 'Drone']);
        $kategoriAudio  = Kategori::create(['kategori_nama' => 'Audio']);

        // 2. Alat
        $alat1 = Alat::create([
            'alat_kategori_id'  => $kategoriKamera->kategori_id,
            'alat_nama'         => 'Sony A7III',
            'alat_deskripsi'    => 'Kamera mirrorless full frame, cocok untuk foto & video',
            'alat_hargaperhari' => 150000,
            'alat_stok'         => 5,
        ]);

        $alat2 = Alat::create([
            'alat_kategori_id'  => $kategoriKamera->kategori_id,
            'alat_nama'         => 'Canon EOS 90D',
            'alat_deskripsi'    => 'Kamera DSLR APS-C, cocok untuk pemula hingga menengah',
            'alat_hargaperhari' => 100000,
            'alat_stok'         => 8,
        ]);

        $alat3 = Alat::create([
            'alat_kategori_id'  => $kategoriLensa->kategori_id,
            'alat_nama'         => 'Sony 24-70mm f/2.8 GM',
            'alat_deskripsi'    => 'Lensa zoom standar profesional',
            'alat_hargaperhari' => 80000,
            'alat_stok'         => 4,
        ]);

        $alat4 = Alat::create([
            'alat_kategori_id'  => $kategoriDrone->kategori_id,
            'alat_nama'         => 'DJI Mavic Air 2',
            'alat_deskripsi'    => 'Drone kompak dengan kamera 4K',
            'alat_hargaperhari' => 200000,
            'alat_stok'         => 3,
        ]);

        $alat5 = Alat::create([
            'alat_kategori_id'  => $kategoriAudio->kategori_id,
            'alat_nama'         => 'Rode Wireless GO II',
            'alat_deskripsi'    => 'Mic wireless clip-on dual channel',
            'alat_hargaperhari' => 60000,
            'alat_stok'         => 6,
        ]);

        // 3. Pelanggan
        $pelanggan1 = Pelanggan::create([
            'pelanggan_nama'   => 'Budi Santoso',
            'pelanggan_alamat' => 'Jl. Merdeka No. 10, Surabaya',
            'pelanggan_notelp' => '0812345678901',
            'pelanggan_email'  => 'budi@example.com',
        ]);

        $pelanggan2 = Pelanggan::create([
            'pelanggan_nama'   => 'Siti Aminah',
            'pelanggan_alamat' => 'Jl. Diponegoro No. 25, Sidoarjo',
            'pelanggan_notelp' => '0898765432109',
            'pelanggan_email'  => 'siti@example.com',
        ]);

        $pelanggan3 = Pelanggan::create([
            'pelanggan_nama'   => 'Andi Wijaya',
            'pelanggan_alamat' => 'Jl. Gubeng No. 5, Surabaya',
            'pelanggan_notelp' => '0856112233445',
            'pelanggan_email'  => 'andi@example.com',
        ]);

        // 4. Pelanggan Data
        // Catatan: 'pelanggan_data_file' di sini cuma path string dummy,
        // bukan file fisik asli. Ini cukup untuk testing GET/relasi,
        // tapi kalau mau test upload sungguhan, pakai request
        // "Create Pelanggan Data (upload file valid)" di Postman.
        PelangganData::create([
            'pelanggan_data_pelanggan_id' => $pelanggan1->pelanggan_id,
            'pelanggan_data_jenis'        => 'KTP',
            'pelanggan_data_file'         => 'pelanggan_data/dummy-ktp-budi.jpg',
        ]);

        PelangganData::create([
            'pelanggan_data_pelanggan_id' => $pelanggan2->pelanggan_id,
            'pelanggan_data_jenis'        => 'SIM',
            'pelanggan_data_file'         => 'pelanggan_data/dummy-sim-siti.jpg',
        ]);

        // 5. Penyewaan
        $penyewaan1 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan1->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-05',
            'penyewaan_tglkembali'     => '2026-08-08',
            'penyewaan_sttspembayaran' => 'Lunas',
            'penyewaan_sttskembali'    => 'Sudah Kembali',
            'penyewaan_totalharga'     => 690000,
        ]);

        $penyewaan2 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan2->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-10',
            'penyewaan_tglkembali'     => '2026-08-12',
            'penyewaan_sttspembayaran' => 'DP',
            'penyewaan_sttskembali'    => 'Belum Kembali',
            'penyewaan_totalharga'     => 400000,
        ]);

        $penyewaan3 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan3->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-15',
            'penyewaan_tglkembali'     => '2026-08-16',
            'penyewaan_sttspembayaran' => 'Belum Dibayar',
            'penyewaan_sttskembali'    => 'Belum Kembali',
            'penyewaan_totalharga'     => 260000,
        ]);

        // 6. Penyewaan Detail
        PenyewaanDetail::create([
            'penyewaan_detail_penyewaan_id' => $penyewaan1->penyewaan_id,
            'penyewaan_detail_alat_id'      => $alat1->alat_id,
            'penyewaan_detail_jumlah'       => 1,
            'penyewaan_detail_subharga'     => 450000,
        ]);

        PenyewaanDetail::create([
            'penyewaan_detail_penyewaan_id' => $penyewaan1->penyewaan_id,
            'penyewaan_detail_alat_id'      => $alat3->alat_id,
            'penyewaan_detail_jumlah'       => 1,
            'penyewaan_detail_subharga'     => 240000,
        ]);

        PenyewaanDetail::create([
            'penyewaan_detail_penyewaan_id' => $penyewaan2->penyewaan_id,
            'penyewaan_detail_alat_id'      => $alat4->alat_id,
            'penyewaan_detail_jumlah'       => 2,
            'penyewaan_detail_subharga'     => 400000,
        ]);

        PenyewaanDetail::create([
            'penyewaan_detail_penyewaan_id' => $penyewaan3->penyewaan_id,
            'penyewaan_detail_alat_id'      => $alat2->alat_id,
            'penyewaan_detail_jumlah'       => 1,
            'penyewaan_detail_subharga'     => 100000,
        ]);

        PenyewaanDetail::create([
            'penyewaan_detail_penyewaan_id' => $penyewaan3->penyewaan_id,
            'penyewaan_detail_alat_id'      => $alat5->alat_id,
            'penyewaan_detail_jumlah'       => 1,
            'penyewaan_detail_subharga'     => 60000,
        ]);
    }
}