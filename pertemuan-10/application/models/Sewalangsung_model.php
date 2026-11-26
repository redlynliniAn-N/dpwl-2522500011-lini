<?php

class Sewalangsung_model
{
    public const DP_PERSEN = 30;          // sama dengan alur online
    public const MAKS_HARI = 127;         // t_pilih.n_hari tinyint
    public const MAKS_UNIT = 10;          // batas unit per transaksi
    public const MAKS_NOMINAL = 8388607;  // t_bayar.nominal mediumint
    public const TOLERANSI_MENIT = 60;    // toleransi waktu ambil yang sudah lewat

    // Status t_sewa yang dianggap memegang kendaraan pada suatu periode.
    private const STATUS_MENAHAN_UNIT = "'disetujui','aktif'";

    // Fragmen WHERE: transaksi dengan pembayaran pertama = tunai.
    private const FILTER_LANGSUNG = "(SELECT b0.mtode_byr
        FROM t_bayar b0
        WHERE b0.kode_sewa = s.kode_sewa
        ORDER BY b0.tgl_wkt_byr ASC, b0.kode_bayar ASC
        LIMIT 1) = 'tunai'";

    private mysqli $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /* ------------------------------------------------------------------
     * Helper query (prepared statement)
     * ---------------------------------------------------------------- */

    private function jalankan(
        string $sql,
        string $tipe = '',
        array $param = []
    ): mysqli_stmt {
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

    private function ambilSemua(
        string $sql,
        string $tipe = '',
        array $param = []
    ): array {
        $stmt = $this->jalankan($sql, $tipe, $param);
        $baris = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $baris;
    }

    private function ambilSatu(
        string $sql,
        string $tipe = '',
        array $param = []
    ): ?array {
        $stmt = $this->jalankan($sql, $tipe, $param);
        $baris = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $baris ?: null;
    }

    private function ubah(
        string $sql,
        string $tipe = '',
        array $param = []
    ): int {
        $stmt = $this->jalankan($sql, $tipe, $param);
        $terpengaruh = $stmt->affected_rows;
        $stmt->close();
        return (int) $terpengaruh;
    }

    private function ambilKunci(string $nama): void
    {
        $r = $this->ambilSatu('SELECT GET_LOCK(?, 10) AS k', 's', [$nama]);
        if ((int) ($r['k'] ?? 0) !== 1) {
            throw new RuntimeException('Kunci proses tidak diperoleh.');
        }
    }

    private function lepasKunci(string $nama): void
    {
        try {
            $this->ambilSatu('SELECT RELEASE_LOCK(?) AS k', 's', [$nama]);
        } catch (Throwable $e) {
            error_log('Lepas kunci gagal: ' . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------
     * READ
     * ---------------------------------------------------------------- */

    /** Daftar header transaksi sewa langsung beserta ringkasan detail. */
    public function getAll(): array
    {
        $sql = "SELECT
                s.kode_sewa, s.tgl_mulai, s.tgl_selesai, s.wkt_ambil,
                s.status_sewa, s.user_plg, p.nm_lngkp,
                (SELECT COUNT(*) FROM t_pilih d
                    WHERE d.kode_sewa = s.kode_sewa) AS jml_unit,
                (SELECT COALESCE(SUM(d.hrg_sewa * d.n_hari), 0)
                    FROM t_pilih d
                    WHERE d.kode_sewa = s.kode_sewa) AS total,
                (SELECT COALESCE(SUM(b.nominal), 0)
                    FROM t_bayar b
                    WHERE b.kode_sewa = s.kode_sewa
                    AND b.sttus_byr = 'valid') AS dibayar
            FROM t_sewa s
            INNER JOIN t_penyewa p ON p.user_plg = s.user_plg
            WHERE " . self::FILTER_LANGSUNG . "
            ORDER BY s.kode_sewa DESC";
        return $this->ambilSemua($sql);
    }

    public function getPenyewa(): array
    {
        return $this->ambilSemua(
            "SELECT user_plg, nm_lngkp, no_hp, nik
            FROM t_penyewa
            ORDER BY nm_lngkp ASC"
        );
    }

    /** Kendaraan yang boleh dipilih pada formulir. */
    public function getMobilTersedia(): array
    {
        return $this->ambilSemua(
            "SELECT no_plat, nm_mobil, jns_mobil, transmisi, nkursi, hrg_hari
            FROM t_mobil
            WHERE status_unit = 'tersedia'
            ORDER BY nm_mobil ASC, no_plat ASC"
        );
    }

    /** Header + detail + pembayaran satu transaksi sewa langsung. */
    public function getDetail(string $kodeSewa): ?array
    {
        $header = $this->ambilSatu(
            "SELECT s.kode_sewa, s.tgl_mulai, s.tgl_selesai, s.wkt_ambil,
                s.status_sewa, s.user_plg,
                p.nm_lngkp, p.no_hp, p.nik, p.almt_lngkp
            FROM t_sewa s
            INNER JOIN t_penyewa p ON p.user_plg = s.user_plg
            WHERE s.kode_sewa = ?
            AND " . self::FILTER_LANGSUNG . "
            LIMIT 1",
            's',
            [$kodeSewa]
        );
        if ($header === null) {
            return null;
        }
        $header['detail'] = $this->ambilSemua(
            "SELECT d.no_plat, d.hrg_sewa, d.n_hari,
                m.nm_mobil, m.jns_mobil, m.transmisi, m.status_unit
            FROM t_pilih d
            INNER JOIN t_mobil m ON m.no_plat = d.no_plat
            WHERE d.kode_sewa = ?
            ORDER BY d.no_plat ASC",
            's',
            [$kodeSewa]
        );
        $header['bayar'] = $this->ambilSemua(
            "SELECT kode_bayar, tgl_wkt_byr, mtode_byr, jns_byr,
                nominal, sttus_byr
            FROM t_bayar
            WHERE kode_sewa = ?
            ORDER BY tgl_wkt_byr ASC, kode_bayar ASC",
            's',
            [$kodeSewa]
        );
        $total = 0;
        foreach ($header['detail'] as $d) {
            $total += (int) $d['hrg_sewa'] * (int) $d['n_hari'];
        }
        $dibayar = 0;
        foreach ($header['bayar'] as $b) {
            if ($b['sttus_byr'] === 'valid') {
                $dibayar += (int) $b['nominal'];
            }
        }
        $header['total'] = $total;
        $header['dibayar'] = $dibayar;
        $header['sisa'] = max(0, $total - $dibayar);
        return $header;
    }

    /* ------------------------------------------------------------------
     * Perhitungan & aturan bisnis murni (tanpa DB)
     * ---------------------------------------------------------------- */

    public static function hitungDpMinimal(int $total): int
    {
        return (int) round(($total * self::DP_PERSEN) / 100);
    }

    /**
     * Aturan periode sewa langsung. Mengembalikan jumlah hari.
     * @throws InvalidArgumentException
     */
    private function validasiPeriode(
        string $tglMulai,
        string $tglSelesai,
        string $wktAmbil
    ): int {
        $tz = new DateTimeZone('Asia/Jakarta');
        $sekarang = new DateTimeImmutable('now', $tz);
        $mulai = DateTimeImmutable::createFromFormat('!Y-m-d', $tglMulai, $tz);
        $selesai = DateTimeImmutable::createFromFormat('!Y-m-d', $tglSelesai, $tz);
        if ($mulai === false || $selesai === false) {
            throw new InvalidArgumentException('Format tanggal tidak valid.');
        }

        // Sewa langsung = kendaraan dibawa pada hari transaksi.
        if ($tglMulai !== $sekarang->format('Y-m-d')) {
            throw new InvalidArgumentException(
                'Sewa langsung harus dimulai hari ini ('
                . $sekarang->format('d/m/Y') . ').'
            );
        }
        if ($selesai <= $mulai) {
            throw new InvalidArgumentException(
                'Tanggal selesai harus setelah tanggal mulai.'
            );
        }
        $hari = (int) $mulai->diff($selesai)->days;
        if ($hari < 1 || $hari > self::MAKS_HARI) {
            throw new InvalidArgumentException(
                'Lama sewa harus antara 1 dan ' . self::MAKS_HARI . ' hari.'
            );
        }
        $waktuAmbil = DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i:s',
            $tglMulai . ' ' . $wktAmbil,
            $tz
        );
        $batasBawah = $sekarang->modify('-' . self::TOLERANSI_MENIT . ' minutes');
        if ($waktuAmbil === false || $waktuAmbil < $batasBawah) {
            throw new InvalidArgumentException(
                'Waktu pengambilan tidak boleh lebih dari '
                . self::TOLERANSI_MENIT . ' menit yang lalu.'
            );
        }
        return $hari;
    }

    /* ------------------------------------------------------------------
     * Generator kode (kunci yang sama dengan Transaksi_model agar
     * transaksi online dan langsung tidak bentrok nomor urut)
     * ---------------------------------------------------------------- */

    private function kodeBerikutnya(
        string $prefix,
        string $tabel,
        string $kolom
    ): string {
        // $tabel & $kolom konstanta internal, bukan input pengguna.
        $baris = $this->ambilSatu(
            "SELECT $kolom AS kode FROM $tabel
            WHERE $kolom LIKE ?
            ORDER BY $kolom DESC
            LIMIT 1
            FOR UPDATE",
            's',
            [$prefix . '%']
        );
        $nomor = $baris === null ? 0 : (int) substr($baris['kode'], -2);
        $nomor++;
        if ($nomor > 99) {
            throw new InvalidArgumentException(
                'Nomor harian ' . $prefix . ' telah mencapai batas 99.'
            );
        }
        return $prefix . str_pad((string) $nomor, 2, '0', STR_PAD_LEFT);
    }

    /* ------------------------------------------------------------------
     * CREATE: transaksi header-detail-pembayaran (atomik)
     * ---------------------------------------------------------------- */

    /**
     * @param array $in user_plg, tgl_mulai, tgl_selesai, wkt_ambil (H:i:s),
     *                  no_plat (array), nominal (int)
     * @return string kode_sewa yang terbentuk
     * @throws InvalidArgumentException pelanggaran aturan bisnis
     * @throws RuntimeException         kegagalan sistem
     */
    public function simpan(array $in): string
    {
        $userPlg = (string) $in['user_plg'];
        $plat = array_values(array_unique(array_map('strval', $in['no_plat'])));
        sort($plat, SORT_STRING); // urutan kunci konsisten -> cegah deadlock
        $nominal = (int) $in['nominal'];

        // --- Aturan yang tidak butuh DB (gagal cepat) ---
        if (count($plat) < 1) {
            throw new InvalidArgumentException('Pilih minimal satu kendaraan.');
        }
        if (count($plat) > self::MAKS_UNIT) {
            throw new InvalidArgumentException(
                'Maksimal ' . self::MAKS_UNIT . ' kendaraan per transaksi.'
            );
        }
        $hari = $this->validasiPeriode(
            $in['tgl_mulai'],
            $in['tgl_selesai'],
            $in['wkt_ambil']
        );

        $tz = new DateTimeZone('Asia/Jakarta');
        $sekarang = new DateTimeImmutable('now', $tz);
        $tglKode = $sekarang->format('Ymd');
        $waktuSekarang = $sekarang->format('Y-m-d H:i:s');
        $awalSewa = $in['tgl_mulai'] . ' ' . $in['wkt_ambil'];
        $akhirSewa = $in['tgl_selesai'] . ' ' . $in['wkt_ambil'];

        $kunciInv = 'dpwl_inv_' . $tglKode;   // sama dgn Transaksi_model
        $kunciPay = 'dpwl_pay_' . $tglKode;
        $kunciDidapat = [];

        $this->db->begin_transaction();
        try {
            // 1. Penyewa harus terdaftar.
            $penyewa = $this->ambilSatu(
                'SELECT user_plg FROM t_penyewa WHERE user_plg = ? LIMIT 1',
                's',
                [$userPlg]
            );
            if ($penyewa === null) {
                throw new InvalidArgumentException('Penyewa tidak terdaftar.');
            }

            // 2. Kunci baris kendaraan (urut no_plat) lalu validasi status.
            $holder = implode(',', array_fill(0, count($plat), '?'));
            $mobil = $this->ambilSemua(
                "SELECT no_plat, nm_mobil, hrg_hari, status_unit
                FROM t_mobil
                WHERE no_plat IN ($holder)
                ORDER BY no_plat ASC
                FOR UPDATE",
                str_repeat('s', count($plat)),
                $plat
            );
            if (count($mobil) !== count($plat)) {
                throw new InvalidArgumentException(
                    'Ada kendaraan yang tidak ditemukan.'
                );
            }
            foreach ($mobil as $m) {
                if ($m['status_unit'] !== 'tersedia') {
                    throw new InvalidArgumentException(
                        $m['nm_mobil'] . ' (' . $m['no_plat']
                        . ') tidak berstatus tersedia.'
                    );
                }
            }

            // 3. Tidak boleh bentrok dengan transaksi yang menahan unit.
            //    Overlap: mulai_lama < akhir_baru AND akhir_lama > awal_baru
            foreach ($mobil as $m) {
                $bentrok = $this->ambilSatu(
                    "SELECT s.kode_sewa
                    FROM t_pilih d
                    INNER JOIN t_sewa s ON s.kode_sewa = d.kode_sewa
                    WHERE d.no_plat = ?
                    AND s.status_sewa IN (" . self::STATUS_MENAHAN_UNIT . ")
                    AND TIMESTAMP(s.tgl_mulai, s.wkt_ambil) < ?
                    AND TIMESTAMP(s.tgl_selesai, s.wkt_ambil) > ?
                    LIMIT 1",
                    'sss',
                    [$m['no_plat'], $akhirSewa, $awalSewa]
                );
                if ($bentrok !== null) {
                    throw new InvalidArgumentException(
                        $m['nm_mobil'] . ' (' . $m['no_plat']
                        . ') bentrok jadwal dengan transaksi '
                        . $bentrok['kode_sewa'] . '.'
                    );
                }
            }

            // 4. Total dihitung ULANG di server dari harga master.
            $total = 0;
            foreach ($mobil as $m) {
                $total += (int) $m['hrg_hari'] * $hari;
            }
            if ($total <= 0 || $total > self::MAKS_NOMINAL) {
                throw new InvalidArgumentException(
                    'Total sewa di luar batas yang diizinkan.'
                );
            }

            // 5. Aturan pembayaran: DP minimal 30%, tidak melebihi total.
            $dpMin = self::hitungDpMinimal($total);
            if ($nominal < $dpMin) {
                throw new InvalidArgumentException(
                    'Pembayaran minimal DP ' . self::DP_PERSEN . '% (Rp '
                    . number_format($dpMin, 0, ',', '.') . ').'
                );
            }
            if ($nominal > $total) {
                throw new InvalidArgumentException(
                    'Pembayaran tidak boleh melebihi total sewa (Rp '
                    . number_format($total, 0, ',', '.') . ').'
                );
            }
            $jenisBayar = $nominal === $total ? 'pelunasan' : 'dp';

            // 6. Kode transaksi & kode bayar (di bawah kunci).
            $this->ambilKunci($kunciInv);
            $kunciDidapat[] = $kunciInv;
            $this->ambilKunci($kunciPay);
            $kunciDidapat[] = $kunciPay;
            $kodeSewa = $this->kodeBerikutnya(
                'INV/' . $tglKode . '/',
                't_sewa',
                'kode_sewa'
            );
            $kodeBayar = $this->kodeBerikutnya(
                'PAY/' . $tglKode . '/',
                't_bayar',
                'kode_bayar'
            );

            // 7. HEADER
            $this->ubah(
                "INSERT INTO t_sewa
                    (kode_sewa, tgl_mulai, tgl_selesai, wkt_ambil,
                     status_sewa, user_plg)
                VALUES (?, ?, ?, ?, 'aktif', ?)",
                'sssss',
                [
                    $kodeSewa,
                    $in['tgl_mulai'],
                    $in['tgl_selesai'],
                    $in['wkt_ambil'],
                    $userPlg,
                ]
            );

            // 8. DETAIL (harga = snapshot harga master saat transaksi)
            foreach ($mobil as $m) {
                $harga = (int) $m['hrg_hari'];
                $n = $this->ubah(
                    "INSERT INTO t_pilih (kode_sewa, no_plat, hrg_sewa, n_hari)
                    VALUES (?, ?, ?, ?)",
                    'ssii',
                    [$kodeSewa, $m['no_plat'], $harga, $hari]
                );
                if ($n !== 1) {
                    throw new RuntimeException('Detail transaksi gagal disimpan.');
                }
            }

            // 9. PEMBAYARAN tunai, langsung valid (diterima admin di tempat)
            $n = $this->ubah(
                "INSERT INTO t_bayar
                    (kode_bayar, tgl_wkt_byr, ft_byr, mtode_byr, jns_byr,
                     nominal, sttus_byr, tgl_wkt_ver, kode_sewa)
                VALUES (?, ?, NULL, 'tunai', ?, ?, 'valid', ?, ?)",
                'sssiss',
                [
                    $kodeBayar,
                    $waktuSekarang,
                    $jenisBayar,
                    $nominal,
                    $waktuSekarang,
                    $kodeSewa,
                ]
            );
            if ($n !== 1) {
                throw new RuntimeException('Pembayaran gagal disimpan.');
            }

            // 10. STATUS UNIT -> disewa (harus mengenai semua unit)
            $n = $this->ubah(
                "UPDATE t_mobil SET status_unit = 'disewa'
                WHERE no_plat IN ($holder) AND status_unit = 'tersedia'",
                str_repeat('s', count($plat)),
                $plat
            );
            if ($n !== count($plat)) {
                throw new RuntimeException('Status kendaraan gagal diperbarui.');
            }

            // 11. Verifikasi konsistensi header-detail sebelum commit.
            $cek = $this->ambilSatu(
                "SELECT COUNT(*) AS jml,
                    COALESCE(SUM(hrg_sewa * n_hari), 0) AS total,
                    COUNT(DISTINCT n_hari) AS ragam_hari
                FROM t_pilih WHERE kode_sewa = ?",
                's',
                [$kodeSewa]
            );
            if (
                (int) $cek['jml'] !== count($plat)
                || (int) $cek['total'] !== $total
                || (int) $cek['ragam_hari'] !== 1
            ) {
                throw new RuntimeException('Konsistensi header-detail gagal.');
            }

            $this->db->commit();
            return $kodeSewa;
        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        } finally {
            foreach ($kunciDidapat as $k) {
                $this->lepasKunci($k);
            }
        }
    }

    /* ------------------------------------------------------------------
     * Pembatalan (membalik efek simpan() secara konsisten)
     * ---------------------------------------------------------------- */

    /**
     * Hanya transaksi sewa langsung berstatus 'aktif' yang bisa dibatalkan.
     * Unit dikembalikan ke 'tersedia' kecuali masih ditahan transaksi lain.
     */
    public function batalkan(string $kodeSewa): void
    {
        $this->db->begin_transaction();
        try {
            $header = $this->ambilSatu(
                "SELECT s.kode_sewa, s.status_sewa
                FROM t_sewa s
                WHERE s.kode_sewa = ?
                AND " . self::FILTER_LANGSUNG . "
                LIMIT 1
                FOR UPDATE",
                's',
                [$kodeSewa]
            );
            if ($header === null) {
                throw new InvalidArgumentException(
                    'Transaksi sewa langsung tidak ditemukan.'
                );
            }
            if ($header['status_sewa'] !== 'aktif') {
                throw new InvalidArgumentException(
                    'Hanya transaksi berstatus aktif yang dapat dibatalkan.'
                );
            }

            $unit = $this->ambilSemua(
                "SELECT d.no_plat
                FROM t_pilih d
                WHERE d.kode_sewa = ?
                ORDER BY d.no_plat ASC",
                's',
                [$kodeSewa]
            );
            foreach ($unit as $u) {
                $this->ambilSatu(
                    'SELECT no_plat FROM t_mobil WHERE no_plat = ? FOR UPDATE',
                    's',
                    [$u['no_plat']]
                );
            }

            $n = $this->ubah(
                "UPDATE t_sewa SET status_sewa = 'batal'
                WHERE kode_sewa = ? AND status_sewa = 'aktif'",
                's',
                [$kodeSewa]
            );
            if ($n !== 1) {
                throw new RuntimeException('Status transaksi gagal diperbarui.');
            }

            foreach ($unit as $u) {
                $this->ubah(
                    "UPDATE t_mobil m
                    SET m.status_unit = 'tersedia'
                    WHERE m.no_plat = ?
                    AND m.status_unit = 'disewa'
                    AND NOT EXISTS (
                        SELECT 1
                        FROM t_pilih d
                        INNER JOIN t_sewa s2 ON s2.kode_sewa = d.kode_sewa
                        WHERE d.no_plat = m.no_plat
                        AND s2.kode_sewa <> ?
                        AND s2.status_sewa IN (" . self::STATUS_MENAHAN_UNIT . ")
                    )",
                    'ss',
                    [$u['no_plat'], $kodeSewa]
                );
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
