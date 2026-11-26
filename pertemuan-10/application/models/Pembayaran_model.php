<?php
// Model pembayaran DP: dipakai Pembayaran_controller (penyewa)
// dan Verifikasi_controller (admin).
class Pembayaran_model
{
    private mysqli $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /* ------------------------------------------------------------------
     * Utilitas query
     * ---------------------------------------------------------------- */

    private function jalankan(string $sql, string $tipe = '', array $param = []): mysqli_stmt
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Query gagal disiapkan.');
        }
        if ($tipe !== '') {
            $stmt->bind_param($tipe, ...$param);
        }
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Query gagal dijalankan.');
        }
        return $stmt;
    }

    private function ambilSemua(string $sql, string $tipe = '', array $param = []): array
    {
        $stmt = $this->jalankan($sql, $tipe, $param);
        $hasil = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $hasil;
    }

    private function ambilSatu(string $sql, string $tipe = '', array $param = []): ?array
    {
        $baris = $this->ambilSemua($sql, $tipe, $param);
        return $baris[0] ?? null;
    }

    /** Menjalankan UPDATE dan mengembalikan jumlah baris terpengaruh. */
    private function ubah(string $sql, string $tipe, array $param): int
    {
        $stmt = $this->jalankan($sql, $tipe, $param);
        $n = $stmt->affected_rows;
        $stmt->close();
        return $n;
    }

    private const KOLOM = "b.kode_bayar, b.kode_sewa, b.tgl_wkt_byr, b.ft_byr,
        b.mtode_byr, b.jns_byr, b.nominal, b.sttus_byr, b.tgl_wkt_ver,
        s.tgl_mulai, s.tgl_selesai, s.wkt_ambil, s.status_sewa, s.user_plg";

    /* ------------------------------------------------------------------
     * Sisi penyewa
     * ---------------------------------------------------------------- */

    public function getDaftarPenyewa(string $userPlg): array
    {
        return $this->ambilSemua(
            'SELECT ' . self::KOLOM . "
             FROM t_bayar b
             INNER JOIN t_sewa s ON s.kode_sewa = b.kode_sewa
             WHERE s.user_plg = ? AND b.jns_byr = 'dp'
             ORDER BY b.tgl_wkt_byr DESC, b.kode_bayar DESC",
            's',
            [$userPlg]
        );
    }

    /** Hanya mengembalikan data jika pembayaran milik penyewa tersebut. */
    public function getDetailPenyewa(string $kodeBayar, string $userPlg): ?array
    {
        return $this->ambilSatu(
            'SELECT ' . self::KOLOM . "
             FROM t_bayar b
             INNER JOIN t_sewa s ON s.kode_sewa = b.kode_sewa
             WHERE b.kode_bayar = ? AND s.user_plg = ? AND b.jns_byr = 'dp'
             LIMIT 1",
            'ss',
            [$kodeBayar, $userPlg]
        );
    }

    public function getKendaraan(string $kodeSewa): array
    {
        return $this->ambilSemua(
            'SELECT p.no_plat, m.nm_mobil, p.hrg_sewa, p.n_hari
             FROM t_pilih p
             INNER JOIN t_mobil m ON m.no_plat = p.no_plat
             WHERE p.kode_sewa = ?
             ORDER BY p.no_plat ASC',
            's',
            [$kodeSewa]
        );
    }

    /**
     * Menyimpan nama berkas bukti pada t_bayar.
     * Syarat: milik penyewa, DP, status pending, belum diverifikasi,
     * dan transaksi sewa masih 'diajukan'.
     *
     * @return string|null nama berkas lama (untuk dihapus oleh pemanggil)
     * @throws InvalidArgumentException pelanggaran aturan bisnis
     */
    public function simpanBukti(string $kodeBayar, string $userPlg, string $namaBerkas): ?string
    {
        $this->db->begin_transaction();
        try {
            $baris = $this->ambilSatu(
                "SELECT b.ft_byr, b.sttus_byr, b.tgl_wkt_ver, s.status_sewa
                 FROM t_bayar b
                 INNER JOIN t_sewa s ON s.kode_sewa = b.kode_sewa
                 WHERE b.kode_bayar = ? AND s.user_plg = ? AND b.jns_byr = 'dp'
                 LIMIT 1 FOR UPDATE",
                'ss',
                [$kodeBayar, $userPlg]
            );
            if ($baris === null) {
                throw new InvalidArgumentException('Data pembayaran tidak ditemukan.');
            }
            if (
                $baris['sttus_byr'] !== 'pending'
                || $baris['tgl_wkt_ver'] !== null
                || $baris['status_sewa'] !== 'diajukan'
            ) {
                throw new InvalidArgumentException(
                    'Pembayaran ini sudah diproses atau transaksinya tidak lagi menunggu pembayaran, '
                    . 'sehingga bukti tidak dapat diubah.'
                );
            }

            $n = $this->ubah(
                "UPDATE t_bayar
                 SET ft_byr = ?, tgl_wkt_byr = NOW()
                 WHERE kode_bayar = ? AND sttus_byr = 'pending' AND tgl_wkt_ver IS NULL",
                'ss',
                [$namaBerkas, $kodeBayar]
            );
            if ($n !== 1) {
                throw new RuntimeException('Data pembayaran gagal diperbarui.');
            }

            $this->db->commit();
            return $baris['ft_byr'] !== null && $baris['ft_byr'] !== '' ? $baris['ft_byr'] : null;
        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /* ------------------------------------------------------------------
     * Sisi admin
     * ---------------------------------------------------------------- */

    /** @param string|null $status pending|valid|invalid, null = semua */
    public function getDaftarAdmin(?string $status = null): array
    {
        $sql = 'SELECT ' . self::KOLOM . ", p.nm_lngkp, p.no_hp
                FROM t_bayar b
                INNER JOIN t_sewa s ON s.kode_sewa = b.kode_sewa
                INNER JOIN t_penyewa p ON p.user_plg = s.user_plg
                WHERE b.jns_byr = 'dp' AND b.mtode_byr = 'transfer'";
        $tipe = '';
        $param = [];
        if (in_array($status, ['pending', 'valid', 'invalid'], true)) {
            $sql .= ' AND b.sttus_byr = ?';
            $tipe = 's';
            $param = [$status];
        }
        $sql .= ' ORDER BY (b.sttus_byr = \'pending\') DESC, b.tgl_wkt_byr DESC, b.kode_bayar DESC';
        return $this->ambilSemua($sql, $tipe, $param);
    }

    public function hitungPerStatus(): array
    {
        $hasil = ['pending' => 0, 'valid' => 0, 'invalid' => 0];
        $baris = $this->ambilSemua(
            "SELECT sttus_byr, COUNT(*) AS jml FROM t_bayar
             WHERE jns_byr = 'dp' AND mtode_byr = 'transfer'
             GROUP BY sttus_byr"
        );
        foreach ($baris as $b) {
            $hasil[$b['sttus_byr']] = (int) $b['jml'];
        }
        return $hasil;
    }

    public function getDetailAdmin(string $kodeBayar): ?array
    {
        return $this->ambilSatu(
            'SELECT ' . self::KOLOM . ", p.nm_lngkp, p.no_hp, p.nik,
                (SELECT COALESCE(SUM(t.hrg_sewa * t.n_hari), 0)
                 FROM t_pilih t WHERE t.kode_sewa = b.kode_sewa) AS total_sewa
             FROM t_bayar b
             INNER JOIN t_sewa s ON s.kode_sewa = b.kode_sewa
             INNER JOIN t_penyewa p ON p.user_plg = s.user_plg
             WHERE b.kode_bayar = ? AND b.jns_byr = 'dp'
             LIMIT 1",
            's',
            [$kodeBayar]
        );
    }

    /**
     * Admin menerima ('valid') atau menolak ('invalid') pembayaran DP.
     * t_bayar dan t_sewa diubah dalam satu transaksi database:
     *   valid   -> t_bayar.sttus_byr = valid,   t_sewa.status_sewa = disetujui
     *   invalid -> t_bayar.sttus_byr = invalid, t_sewa.status_sewa = ditolak
     *
     * @throws InvalidArgumentException pelanggaran aturan bisnis
     */
    public function verifikasi(string $kodeBayar, string $keputusan): void
    {
        if (!in_array($keputusan, ['valid', 'invalid'], true)) {
            throw new InvalidArgumentException('Keputusan verifikasi tidak valid.');
        }
        $statusSewaBaru = $keputusan === 'valid' ? 'disetujui' : 'ditolak';

        $this->db->begin_transaction();
        try {
            $baris = $this->ambilSatu(
                "SELECT b.kode_sewa, b.ft_byr, b.sttus_byr, b.tgl_wkt_ver, s.status_sewa
                 FROM t_bayar b
                 INNER JOIN t_sewa s ON s.kode_sewa = b.kode_sewa
                 WHERE b.kode_bayar = ? AND b.jns_byr = 'dp'
                 LIMIT 1 FOR UPDATE",
                's',
                [$kodeBayar]
            );
            if ($baris === null) {
                throw new InvalidArgumentException('Data pembayaran tidak ditemukan.');
            }
            if ($baris['sttus_byr'] !== 'pending' || $baris['tgl_wkt_ver'] !== null) {
                throw new InvalidArgumentException('Pembayaran ini sudah diverifikasi sebelumnya.');
            }
            if ($baris['status_sewa'] !== 'diajukan') {
                throw new InvalidArgumentException(
                    'Status transaksi sewa bukan "diajukan", sehingga pembayaran tidak dapat diverifikasi.'
                );
            }
            if ($keputusan === 'valid' && trim((string) $baris['ft_byr']) === '') {
                throw new InvalidArgumentException(
                    'Penyewa belum mengunggah bukti pembayaran, sehingga pembayaran belum dapat diterima.'
                );
            }

            $n = $this->ubah(
                "UPDATE t_bayar
                 SET sttus_byr = ?, tgl_wkt_ver = NOW()
                 WHERE kode_bayar = ? AND sttus_byr = 'pending'",
                'ss',
                [$keputusan, $kodeBayar]
            );
            if ($n !== 1) {
                throw new RuntimeException('Status pembayaran gagal diperbarui.');
            }

            $n = $this->ubah(
                "UPDATE t_sewa
                 SET status_sewa = ?
                 WHERE kode_sewa = ? AND status_sewa = 'diajukan'",
                'ss',
                [$statusSewaBaru, $baris['kode_sewa']]
            );
            if ($n !== 1) {
                throw new RuntimeException('Status transaksi sewa gagal diperbarui.');
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * P10: Mengambil daftar transaksi yang dapat diproses pelunasannya.
     *
     * Total transaksi menggunakan snapshot tarif pada t_pilih.
     * Pembayaran yang mengurangi kewajiban hanya yang berstatus valid.
     */
    public function getDaftarPelunasan(): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                s.kode_sewa,
                s.tgl_mulai,
                s.tgl_selesai,
                s.status_sewa,
                p.user_plg,
                p.nm_lngkp,
                p.no_hp,
                COALESCE((
                    SELECT SUM(tp.hrg_sewa * tp.n_hari)
                    FROM t_pilih tp
                    WHERE tp.kode_sewa = s.kode_sewa
                ), 0) AS total_transaksi,
                COALESCE((
                    SELECT SUM(b.nominal)
                    FROM t_bayar b
                    WHERE b.kode_sewa = s.kode_sewa
                      AND b.sttus_byr = 'valid'
                ), 0) AS total_terbayar
             FROM t_sewa s
             INNER JOIN t_penyewa p
                ON p.user_plg = s.user_plg
             WHERE s.status_sewa IN ('disetujui', 'lunas')
               AND EXISTS (
                   SELECT 1
                   FROM t_pilih tp
                   WHERE tp.kode_sewa = s.kode_sewa
               )
             ORDER BY s.tgl_mulai DESC, s.kode_sewa DESC"
        );
        if (!$stmt) {
            throw new RuntimeException(
                'Query daftar pelunasan gagal disiapkan.'
            );
        }
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException(
                'Daftar pelunasan gagal diambil.'
            );
        }
        $daftar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($daftar as &$item) {
            $item['total_transaksi'] = (int) $item['total_transaksi'];
            $item['total_terbayar'] = (int) $item['total_terbayar'];
            $item['sisa_kewajiban'] = max(
                0,
                $item['total_transaksi'] - $item['total_terbayar']
            );
        }
        unset($item);

        return array_values(array_filter(
            $daftar,
            static fn(array $item): bool =>
                $item['sisa_kewajiban'] > 0 ||
                $item['status_sewa'] === 'lunas'
        ));
    }

    /**
     * P10: Mengambil detail transaksi, penyewa, dan riwayat pembayaran
     * untuk halaman pelunasan.
     */
    public function getDetailPelunasan(string $kodeSewa): ?array
    {
        $kodeSewa = trim($kodeSewa);
        if ($kodeSewa === '') {
            throw new InvalidArgumentException(
                'Kode transaksi sewa wajib diisi.'
            );
        }

        $stmt = $this->db->prepare(
            "SELECT
                s.kode_sewa,
                s.tgl_mulai,
                s.tgl_selesai,
                s.wkt_ambil,
                s.status_sewa,
                p.user_plg,
                p.nm_lngkp,
                p.no_hp,
                p.nik,
                p.almt_lngkp,
                COALESCE((
                    SELECT SUM(tp.hrg_sewa * tp.n_hari)
                    FROM t_pilih tp
                    WHERE tp.kode_sewa = s.kode_sewa
                ), 0) AS total_transaksi,
                COALESCE((
                    SELECT SUM(b.nominal)
                    FROM t_bayar b
                    WHERE b.kode_sewa = s.kode_sewa
                      AND b.sttus_byr = 'valid'
                ), 0) AS total_terbayar
             FROM t_sewa s
             INNER JOIN t_penyewa p
                ON p.user_plg = s.user_plg
             WHERE s.kode_sewa = ?
             LIMIT 1"
        );
        if (!$stmt) {
            throw new RuntimeException(
                'Query detail pelunasan gagal disiapkan.'
            );
        }
        $stmt->bind_param('s', $kodeSewa);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException(
                'Detail transaksi pelunasan gagal diambil.'
            );
        }
        $detail = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($detail === null) {
            return null;
        }

        $detail['total_transaksi'] = (int) $detail['total_transaksi'];
        $detail['total_terbayar'] = (int) $detail['total_terbayar'];
        $detail['sisa_kewajiban'] = max(
            0,
            $detail['total_transaksi'] - $detail['total_terbayar']
        );

        $stmt = $this->db->prepare(
            "SELECT
                kode_bayar,
                tgl_wkt_byr,
                mtode_byr,
                jns_byr,
                nominal,
                sttus_byr,
                ft_byr,
                tgl_wkt_ver
             FROM t_bayar
             WHERE kode_sewa = ?
             ORDER BY tgl_wkt_byr ASC, kode_bayar ASC"
        );
        if (!$stmt) {
            throw new RuntimeException(
                'Query riwayat pembayaran gagal disiapkan.'
            );
        }
        $stmt->bind_param('s', $kodeSewa);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException(
                'Riwayat pembayaran gagal diambil.'
            );
        }
        $detail['riwayat_pembayaran'] =
            $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $stmt = $this->db->prepare(
            "SELECT
                m.no_plat,
                m.nm_mobil,
                tp.hrg_sewa,
                tp.n_hari,
                (tp.hrg_sewa * tp.n_hari) AS subtotal
             FROM t_pilih tp
             INNER JOIN t_mobil m
                ON m.no_plat = tp.no_plat
             WHERE tp.kode_sewa = ?
             ORDER BY m.nm_mobil ASC, m.no_plat ASC"
        );
        if (!$stmt) {
            throw new RuntimeException(
                'Query detail kendaraan pelunasan gagal disiapkan.'
            );
        }
        $stmt->bind_param('s', $kodeSewa);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException(
                'Detail kendaraan pelunasan gagal diambil.'
            );
        }
        $detail['daftar_kendaraan'] =
            $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $detail;
    }

    /**
     * P10: Membuat kode pembayaran dengan format PAY/YYYYMMDD/NN.
     * Pemanggil harus memegang named lock pembayaran harian.
     */
    private function buatKodeBayarPelunasan(string $tanggalKode): string
    {
        $pola = 'PAY/' . $tanggalKode . '/%';
        $stmt = $this->db->prepare(
            "SELECT kode_bayar
             FROM t_bayar
             WHERE kode_bayar LIKE ?
             ORDER BY kode_bayar DESC
             LIMIT 1"
        );
        if (!$stmt) {
            throw new RuntimeException(
                'Pembuatan kode pembayaran gagal disiapkan.'
            );
        }
        $stmt->bind_param('s', $pola);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException(
                'Pembuatan kode pembayaran gagal dijalankan.'
            );
        }
        $terakhir = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $nomorBerikutnya = 1;
        if ($terakhir !== null) {
            $bagianKode = explode('/', (string) $terakhir['kode_bayar']);
            $nomorBerikutnya = ((int) end($bagianKode)) + 1;
        }
        if ($nomorBerikutnya > 99) {
            throw new RuntimeException(
                'Nomor pembayaran harian telah mencapai batas 99.'
            );
        }

        return sprintf('PAY/%s/%02d', $tanggalKode, $nomorBerikutnya);
    }

    /**
     * P10: Mencatat pelunasan langsung oleh admin.
     *
     * Pembayaran langsung dicatat sebagai valid.
     * Status transaksi diperbarui menjadi lunas dalam transaksi database
     * yang sama. Status kendaraan tidak diubah pada P10.
     */
    public function simpanPelunasan(
        string $kodeSewa,
        string $metodeBayar,
        ?string $namaFileBukti = null
    ): string {
        $kodeSewa = trim($kodeSewa);
        $metodeBayar = trim($metodeBayar);
        if ($kodeSewa === '') {
            throw new InvalidArgumentException(
                'Kode transaksi sewa wajib diisi.'
            );
        }
        if (!in_array($metodeBayar, ['tunai', 'transfer'], true)) {
            throw new InvalidArgumentException(
                'Metode pembayaran tidak valid.'
            );
        }
        if ($metodeBayar === 'transfer') {
            $namaFileBukti = trim((string) $namaFileBukti);
            if ($namaFileBukti === '') {
                throw new InvalidArgumentException(
                    'Bukti transfer wajib tersedia.'
                );
            }
        } else {
            $namaFileBukti = null;
        }

        // Harus sama dengan kunci yang digunakan Transaksi_model::checkout()
        // ketika membuat kode pembayaran DP.
        $namaKunci = 'dpwl_payment_code_sequence';
        $kunciDiperoleh = false;
        $transaksiDimulai = false;

        try {
            $stmt = $this->db->prepare(
                'SELECT GET_LOCK(?, 10) AS lock_status'
            );
            if (!$stmt) {
                throw new RuntimeException(
                    'Kunci pembuatan kode pembayaran gagal disiapkan.'
                );
            }
            $stmt->bind_param('s', $namaKunci);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new RuntimeException(
                    'Kunci pembuatan kode pembayaran gagal dijalankan.'
                );
            }
            $hasilKunci = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ((int) ($hasilKunci['lock_status'] ?? 0) !== 1) {
                throw new RuntimeException(
                    'Proses pembayaran sedang digunakan. Silakan coba kembali.'
                );
            }
            $kunciDiperoleh = true;

            $this->db->begin_transaction();
            $transaksiDimulai = true;

            // Kunci transaksi agar dua permintaan pelunasan tidak berjalan
            // bersamaan untuk transaksi yang sama.
            $stmt = $this->db->prepare(
                "SELECT
                    s.kode_sewa,
                    s.status_sewa,
                    COALESCE((
                        SELECT SUM(tp.hrg_sewa * tp.n_hari)
                        FROM t_pilih tp
                        WHERE tp.kode_sewa = s.kode_sewa
                    ), 0) AS total_transaksi,
                    COALESCE((
                        SELECT SUM(b.nominal)
                        FROM t_bayar b
                        WHERE b.kode_sewa = s.kode_sewa
                          AND b.sttus_byr = 'valid'
                    ), 0) AS total_terbayar
                 FROM t_sewa s
                 WHERE s.kode_sewa = ?
                 LIMIT 1
                 FOR UPDATE"
            );
            if (!$stmt) {
                throw new RuntimeException(
                    'Pemeriksaan transaksi pelunasan gagal disiapkan.'
                );
            }
            $stmt->bind_param('s', $kodeSewa);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new RuntimeException(
                    'Pemeriksaan transaksi pelunasan gagal dijalankan.'
                );
            }
            $transaksi = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($transaksi === null) {
                throw new RuntimeException(
                    'Transaksi sewa tidak ditemukan.'
                );
            }
            if (!in_array(
                $transaksi['status_sewa'],
                ['disetujui', 'lunas'],
                true
            )) {
                throw new RuntimeException(
                    'Status transaksi belum memenuhi syarat pelunasan.'
                );
            }

            $totalTransaksi = (int) $transaksi['total_transaksi'];
            $totalTerbayar = (int) $transaksi['total_terbayar'];
            $sisaKewajiban = $totalTransaksi - $totalTerbayar;

            if ($totalTransaksi <= 0 || $sisaKewajiban <= 0) {
                throw new RuntimeException(
                    'Transaksi tidak memiliki sisa kewajiban untuk dilunasi.'
                );
            }
            if ($sisaKewajiban > 8388607) {
                throw new RuntimeException(
                    'Sisa kewajiban melebihi kapasitas kolom nominal pada database.'
                );
            }

            // Pastikan transaksi belum memiliki record pelunasan.
            $stmt = $this->db->prepare(
                "SELECT kode_bayar
                 FROM t_bayar
                 WHERE kode_sewa = ?
                   AND jns_byr = 'pelunasan'
                 LIMIT 1
                 FOR UPDATE"
            );
            if (!$stmt) {
                throw new RuntimeException(
                    'Pemeriksaan pelunasan sebelumnya gagal disiapkan.'
                );
            }
            $stmt->bind_param('s', $kodeSewa);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new RuntimeException(
                    'Pemeriksaan pelunasan sebelumnya gagal dijalankan.'
                );
            }
            $pelunasanAda = $stmt->get_result()->fetch_assoc() !== null;
            $stmt->close();

            if ($pelunasanAda) {
                throw new RuntimeException(
                    'Transaksi ini sudah memiliki record pelunasan.'
                );
            }

            $hasilWaktu = $this->db->query(
                "SELECT
                    DATE_FORMAT(NOW(), '%Y%m%d') AS tanggal_kode,
                    NOW() AS waktu_pembayaran"
            );
            if (!$hasilWaktu) {
                throw new RuntimeException(
                    'Waktu pembayaran gagal dibaca.'
                );
            }
            $waktu = $hasilWaktu->fetch_assoc();
            $hasilWaktu->free();

            $tanggalKode = (string) ($waktu['tanggal_kode'] ?? '');
            $waktuPembayaran = (string) ($waktu['waktu_pembayaran'] ?? '');
            if (
                !preg_match('/^\d{8}$/', $tanggalKode) ||
                $waktuPembayaran === ''
            ) {
                throw new RuntimeException(
                    'Waktu pembayaran tidak valid.'
                );
            }

            $kodeBayar = $this->buatKodeBayarPelunasan($tanggalKode);

            $stmt = $this->db->prepare(
                "INSERT INTO t_bayar (
                    kode_bayar,
                    tgl_wkt_byr,
                    ft_byr,
                    mtode_byr,
                    jns_byr,
                    nominal,
                    sttus_byr,
                    tgl_wkt_ver,
                    kode_sewa
                ) VALUES (?, ?, ?, ?, 'pelunasan', ?, 'valid', NOW(), ?)"
            );
            if (!$stmt) {
                throw new RuntimeException(
                    'Pencatatan pelunasan gagal disiapkan.'
                );
            }
            $stmt->bind_param(
                'ssssis',
                $kodeBayar,
                $waktuPembayaran,
                $namaFileBukti,
                $metodeBayar,
                $sisaKewajiban,
                $kodeSewa
            );
            if (!$stmt->execute()) {
                $stmt->close();
                throw new RuntimeException(
                    'Pelunasan gagal disimpan.'
                );
            }
            $stmt->close();

            // Pelunasan berhasil dicatat; perbarui status transaksi
            // dalam transaksi database yang sama.
            $stmt = $this->db->prepare(
                "UPDATE t_sewa
                 SET status_sewa = 'lunas'
                 WHERE kode_sewa = ?
                   AND status_sewa IN ('disetujui', 'lunas')"
            );
            if (!$stmt) {
                throw new RuntimeException(
                    'Pembaruan status transaksi menjadi lunas gagal disiapkan.'
                );
            }
            $stmt->bind_param('s', $kodeSewa);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new RuntimeException(
                    'Status transaksi gagal diperbarui menjadi lunas.'
                );
            }
            if ($stmt->affected_rows !== 1) {
                $stmt->close();
                throw new RuntimeException(
                    'Status transaksi tidak berubah menjadi lunas. '
                    . 'Periksa kembali status transaksi.'
                );
            }
            $stmt->close();

            $this->db->commit();
            $transaksiDimulai = false;

            return $kodeBayar;
        } catch (Throwable $e) {
            if ($transaksiDimulai) {
                $this->db->rollback();
            }
            throw $e;
        } finally {
            if ($kunciDiperoleh) {
                $stmt = $this->db->prepare(
                    'SELECT RELEASE_LOCK(?)'
                );
                if ($stmt) {
                    $stmt->bind_param('s', $namaKunci);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
}