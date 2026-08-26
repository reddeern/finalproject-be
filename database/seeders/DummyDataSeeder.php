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
        // 1. Kategori (5 kategori)
        $kategoriKamera = Kategori::create(['kategori_nama' => 'Kamera']);
        $kategoriLensa  = Kategori::create(['kategori_nama' => 'Lensa']);
        $kategoriDrone  = Kategori::create(['kategori_nama' => 'Drone']);
        $kategoriAudio  = Kategori::create(['kategori_nama' => 'Audio']);
        $kategoriLighting = Kategori::create(['kategori_nama' => 'Lighting']);

        // 2. Alat (15 items)
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
            'alat_kategori_id'  => $kategoriKamera->kategori_id,
            'alat_nama'         => 'Nikon Z9',
            'alat_deskripsi'    => 'Kamera mirrorless profesional flagship',
            'alat_hargaperhari' => 250000,
            'alat_stok'         => 2,
        ]);

        $alat4 = Alat::create([
            'alat_kategori_id'  => $kategoriLensa->kategori_id,
            'alat_nama'         => 'Sony 24-70mm f/2.8 GM',
            'alat_deskripsi'    => 'Lensa zoom standar profesional',
            'alat_hargaperhari' => 80000,
            'alat_stok'         => 4,
        ]);

        $alat5 = Alat::create([
            'alat_kategori_id'  => $kategoriLensa->kategori_id,
            'alat_nama'         => 'Canon EF 70-200mm f/2.8L',
            'alat_deskripsi'    => 'Lensa telephoto profesional',
            'alat_hargaperhari' => 70000,
            'alat_stok'         => 3,
        ]);

        $alat6 = Alat::create([
            'alat_kategori_id'  => $kategoriLensa->kategori_id,
            'alat_nama'         => 'Sigma 35mm f/1.4 DG HSM',
            'alat_deskripsi'    => 'Lensa prime wide angle',
            'alat_hargaperhari' => 50000,
            'alat_stok'         => 6,
        ]);

        $alat7 = Alat::create([
            'alat_kategori_id'  => $kategoriDrone->kategori_id,
            'alat_nama'         => 'DJI Mavic Air 2',
            'alat_deskripsi'    => 'Drone kompak dengan kamera 4K',
            'alat_hargaperhari' => 200000,
            'alat_stok'         => 3,
        ]);

        $alat8 = Alat::create([
            'alat_kategori_id'  => $kategoriDrone->kategori_id,
            'alat_nama'         => 'DJI Phantom 4 Pro V2.0',
            'alat_deskripsi'    => 'Drone professional dengan 1 inch sensor',
            'alat_hargaperhari' => 350000,
            'alat_stok'         => 2,
        ]);

        $alat9 = Alat::create([
            'alat_kategori_id'  => $kategoriAudio->kategori_id,
            'alat_nama'         => 'Rode Wireless GO II',
            'alat_deskripsi'    => 'Mic wireless clip-on dual channel',
            'alat_hargaperhari' => 60000,
            'alat_stok'         => 6,
        ]);

        $alat10 = Alat::create([
            'alat_kategori_id'  => $kategoriAudio->kategori_id,
            'alat_nama'         => 'Sennheiser ME 66',
            'alat_deskripsi'    => 'Shotgun microphone profesional',
            'alat_hargaperhari' => 80000,
            'alat_stok'         => 4,
        ]);

        $alat11 = Alat::create([
            'alat_kategori_id'  => $kategoriAudio->kategori_id,
            'alat_nama'         => 'Shure SM7B',
            'alat_deskripsi'    => 'Studio microphone dynamic premium',
            'alat_hargaperhari' => 90000,
            'alat_stok'         => 5,
        ]);

        $alat12 = Alat::create([
            'alat_kategori_id'  => $kategoriLighting->kategori_id,
            'alat_nama'         => 'Neewer LED Panel 2-Pack',
            'alat_deskripsi'    => 'LED panel 480 LEDs dimmable',
            'alat_hargaperhari' => 120000,
            'alat_stok'         => 7,
        ]);

        $alat13 = Alat::create([
            'alat_kategori_id'  => $kategoriLighting->kategori_id,
            'alat_nama'         => 'Aputure MC 4-Light Kit',
            'alat_deskripsi'    => 'Profesional RGB LED panel kit',
            'alat_hargaperhari' => 400000,
            'alat_stok'         => 1,
        ]);

        $alat14 = Alat::create([
            'alat_kategori_id'  => $kategoriLighting->kategori_id,
            'alat_nama'         => 'Godox SL-60W',
            'alat_deskripsi'    => 'LED studio light 5600K 60W',
            'alat_hargaperhari' => 150000,
            'alat_stok'         => 3,
        ]);

        $alat15 = Alat::create([
            'alat_kategori_id'  => $kategoriKamera->kategori_id,
            'alat_nama'         => 'GoPro Hero 11 Black',
            'alat_deskripsi'    => 'Action camera 4K dengan stabilisasi',
            'alat_hargaperhari' => 120000,
            'alat_stok'         => 5,
        ]);

        // 3. Pelanggan (10 pelanggan)
        $pelanggan1 = Pelanggan::create([
            'pelanggan_nama'   => 'Budi Santoso',
            'pelanggan_alamat' => 'Jl. Merdeka No. 10, Surabaya',
            'pelanggan_notelp' => '0812345678901',
            'pelanggan_email'  => 'budi@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan2 = Pelanggan::create([
            'pelanggan_nama'   => 'Siti Aminah',
            'pelanggan_alamat' => 'Jl. Diponegoro No. 25, Sidoarjo',
            'pelanggan_notelp' => '0898765432109',
            'pelanggan_email'  => 'siti@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan3 = Pelanggan::create([
            'pelanggan_nama'   => 'Andi Wijaya',
            'pelanggan_alamat' => 'Jl. Gubeng No. 5, Surabaya',
            'pelanggan_notelp' => '0856112233445',
            'pelanggan_email'  => 'andi@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan4 = Pelanggan::create([
            'pelanggan_nama'   => 'Maya Putri',
            'pelanggan_alamat' => 'Jl. Ahmad Yani No. 15, Gresik',
            'pelanggan_notelp' => '0834556677889',
            'pelanggan_email'  => 'maya@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan5 = Pelanggan::create([
            'pelanggan_nama'   => 'Lia Cakep',
            'pelanggan_alamat' => 'Jl. Rajawali No. 8, Surabaya',
            'pelanggan_notelp' => '0823334455667',
            'pelanggan_email'  => 'ririn@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan6 = Pelanggan::create([
            'pelanggan_nama'   => 'Fajar Rahman',
            'pelanggan_alamat' => 'Jl. Basuki Rahmat No. 20, Surabaya',
            'pelanggan_notelp' => '0856778899001',
            'pelanggan_email'  => 'fajar@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan7 = Pelanggan::create([
            'pelanggan_nama'   => 'Dewi Lestari',
            'pelanggan_alamat' => 'Jl. Kertajaya No. 30, Surabaya',
            'pelanggan_notelp' => '0878990011223',
            'pelanggan_email'  => 'dewi@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan8 = Pelanggan::create([
            'pelanggan_nama'   => 'Hendra Kusuma',
            'pelanggan_alamat' => 'Jl. Raya Jemur No. 12, Surabaya',
            'pelanggan_notelp' => '0867112233445',
            'pelanggan_email'  => 'hendra@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan9 = Pelanggan::create([
            'pelanggan_nama'   => 'Irene Sungkawa',
            'pelanggan_alamat' => 'Jl. Kali Raya No. 7, Sidoarjo',
            'pelanggan_notelp' => '0845554433221',
            'pelanggan_email'  => 'irene@example.com',
            'pelanggan_password' => 'password123',
        ]);

        $pelanggan10 = Pelanggan::create([
            'pelanggan_nama'   => 'Joko Pratama',
            'pelanggan_alamat' => 'Jl. Nyamuk No. 19, Gresik',
            'pelanggan_notelp' => '0834556677889',
            'pelanggan_email'  => 'joko@example.com',
            'pelanggan_password' => 'password123',
        ]);

        // 4. Pelanggan Data (10 records)
        for ($i = 1; $i <= 10; $i++) {
            $pelanggan = Pelanggan::find($i);
            $jenis = ($i % 2 == 0) ? 'SIM' : 'KTP';
            PelangganData::create([
                'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
                'pelanggan_data_jenis'        => $jenis,
                'pelanggan_data_file'         => "pelanggan_data/dummy-{$jenis}-{$pelanggan->pelanggan_id}.jpg",
            ]);
        }

        // 5. Penyewaan (10 rentals dengan various status)
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

        $penyewaan4 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan4->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-03',
            'penyewaan_tglkembali'     => '2026-08-07',
            'penyewaan_sttspembayaran' => 'Lunas',
            'penyewaan_sttskembali'    => 'Sudah Kembali',
            'penyewaan_totalharga'     => 800000,
        ]);

        $penyewaan5 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan5->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-12',
            'penyewaan_tglkembali'     => '2026-08-14',
            'penyewaan_sttspembayaran' => 'DP',
            'penyewaan_sttskembali'    => 'Sudah Kembali',
            'penyewaan_totalharga'     => 540000,
        ]);

        $penyewaan6 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan6->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-18',
            'penyewaan_tglkembali'     => '2026-08-20',
            'penyewaan_sttspembayaran' => 'Belum Dibayar',
            'penyewaan_sttskembali'    => 'Belum Kembali',
            'penyewaan_totalharga'     => 420000,
        ]);

        $penyewaan7 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan7->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-01',
            'penyewaan_tglkembali'     => '2026-08-04',
            'penyewaan_sttspembayaran' => 'Lunas',
            'penyewaan_sttskembali'    => 'Sudah Kembali',
            'penyewaan_totalharga'     => 360000,
        ]);

        $penyewaan8 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan8->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-20',
            'penyewaan_tglkembali'     => '2026-08-22',
            'penyewaan_sttspembayaran' => 'DP',
            'penyewaan_sttskembali'    => 'Belum Kembali',
            'penyewaan_totalharga'     => 650000,
        ]);

        $penyewaan9 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan9->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-08',
            'penyewaan_tglkembali'     => '2026-08-11',
            'penyewaan_sttspembayaran' => 'Lunas',
            'penyewaan_sttskembali'    => 'Sudah Kembali',
            'penyewaan_totalharga'     => 500000,
        ]);

        $penyewaan10 = Penyewaan::create([
            'penyewaan_pelanggan_id'   => $pelanggan10->pelanggan_id,
            'penyewaan_tglsewa'        => '2026-08-22',
            'penyewaan_tglkembali'     => '2026-08-25',
            'penyewaan_sttspembayaran' => 'Belum Dibayar',
            'penyewaan_sttskembali'    => 'Belum Kembali',
            'penyewaan_totalharga'     => 950000,
        ]);

        // 6. Penyewaan Detail (23 items untuk testing)
        $details = [
            [$penyewaan1->penyewaan_id, $alat1->alat_id, 1, 450000],
            [$penyewaan1->penyewaan_id, $alat4->alat_id, 1, 240000],
            [$penyewaan2->penyewaan_id, $alat7->alat_id, 2, 400000],
            [$penyewaan3->penyewaan_id, $alat2->alat_id, 1, 100000],
            [$penyewaan3->penyewaan_id, $alat9->alat_id, 1, 60000],
            [$penyewaan4->penyewaan_id, $alat3->alat_id, 1, 1000000],
            [$penyewaan5->penyewaan_id, $alat4->alat_id, 1, 160000],
            [$penyewaan5->penyewaan_id, $alat5->alat_id, 1, 140000],
            [$penyewaan5->penyewaan_id, $alat12->alat_id, 1, 240000],
            [$penyewaan6->penyewaan_id, $alat7->alat_id, 1, 200000],
            [$penyewaan6->penyewaan_id, $alat10->alat_id, 1, 160000],
            [$penyewaan6->penyewaan_id, $alat14->alat_id, 1, 60000],
            [$penyewaan7->penyewaan_id, $alat6->alat_id, 1, 100000],
            [$penyewaan7->penyewaan_id, $alat11->alat_id, 1, 90000],
            [$penyewaan7->penyewaan_id, $alat15->alat_id, 1, 120000],
            [$penyewaan8->penyewaan_id, $alat8->alat_id, 1, 700000],
            [$penyewaan9->penyewaan_id, $alat2->alat_id, 1, 300000],
            [$penyewaan9->penyewaan_id, $alat5->alat_id, 1, 140000],
            [$penyewaan9->penyewaan_id, $alat12->alat_id, 1, 60000],
            [$penyewaan10->penyewaan_id, $alat1->alat_id, 1, 450000],
            [$penyewaan10->penyewaan_id, $alat4->alat_id, 1, 240000],
            [$penyewaan10->penyewaan_id, $alat7->alat_id, 1, 260000],
        ];

        foreach ($details as $detail) {
            PenyewaanDetail::create([
                'penyewaan_detail_penyewaan_id' => $detail[0],
                'penyewaan_detail_alat_id'      => $detail[1],
                'penyewaan_detail_jumlah'       => $detail[2],
                'penyewaan_detail_subharga'     => $detail[3],
            ]);
        }
    }
}
