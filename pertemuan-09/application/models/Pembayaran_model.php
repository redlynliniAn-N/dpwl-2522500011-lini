<?php
// P9 BARU - application/models/Pembayaran_model.php
//
// Seluruh operasi pembayaran DP memperbarui record t_bayar yang sudah dibuat
// oleh Transaksi_model::checkout(). Model ini TIDAK PERNAH melakukan INSERT
// ke t_bayar.
//
// Kolom t_bayar yang terbukti dari INSERT di Transaksi_model::checkout():
//   kode_bayar, tgl_wkt_byr, ft_byr, mtode_byr, jns_byr, nominal,
//   sttus_byr, tgl_wkt_ver, kode_sewa

class Pembayaran_model
{
  // TERBUKTI: nilai awal yang ditulis checkout().
  public const STATUS_PENDING = 'pending';

  // ASUMSI - BELUM TERBUKTI. Kode proyek hanya membuktikan 'pending'.
  // Dua nilai berikut HARUS sama persis dengan anggota ENUM t_bayar.sttus_byr
  // di basis data Anda. periksaSkema() memeriksanya otomatis sebelum setiap
  // operasi tulis dan menolak bila tidak cocok, sehingga tidak ada nilai
  // kosong yang tersimpan secara diam-diam.
  public const STATUS_TERVERIFIKASI = 'terverifikasi';
  public const STATUS_DITOLAK = 'ditolak';

  // TERBUKTI: nilai yang ditulis checkout() pada t_sewa.
  private const STATUS_SEWA_DIAJUKAN = 'diajukan';

  private const KOLOM_BAYAR = [
    'kode_bayar',
    'tgl_wkt_byr',
    'ft_byr',
    'mtode_byr',
    'jns_byr',
    'nominal',
    'sttus_byr',
    'tgl_wkt_ver',
    'kode_sewa',
  ];

  // Nama berkas: ft_byr_ + 14 digit + _ + 16 hex + .webp = 43 karakter.
  private const PANJANG_MIN_FT_BYR = 43;

  private static bool $skemaSudahDiperiksa = false;

  private mysqli $db;

  public function __construct()
  {
    global $db;
    $this->db = $db;
  }

  // ---------------------------------------------------------------------
  // Pembantu tampilan status (dipakai View)
  // ---------------------------------------------------------------------

  public static function kunciStatus(?string $sttus, $ftByr): string
  {
    if ($sttus === null) {
      return 'tidak_ada';
    }

    if ($sttus === self::STATUS_TERVERIFIKASI) {
      return 'terverifikasi';
    }

    if ($sttus === self::STATUS_DITOLAK) {
      return 'ditolak';
    }

    if ($sttus === self::STATUS_PENDING) {
      return ($ftByr === null || $ftByr === '')
        ? 'belum_bayar'
        : 'menunggu_verifikasi';
    }

    return 'lainnya';
  }

  public static function tampilStatus(string $kunci): array
  {
    return match ($kunci) {
      'belum_bayar' => [
        'teks' => 'Belum dibayar',
        'bs' => 'bg-secondary',
        'lte' => 'label-default',
      ],
      'menunggu_verifikasi' => [
        'teks' => 'Menunggu verifikasi',
        'bs' => 'bg-warning text-dark',
        'lte' => 'label-warning',
      ],
      'terverifikasi' => [
        'teks' => 'Terverifikasi',
        'bs' => 'bg-success',
        'lte' => 'label-success',
      ],
      'ditolak' => [
        'teks' => 'Ditolak',
        'bs' => 'bg-danger',
        'lte' => 'label-danger',
      ],
      'tidak_ada' => [
        'teks' => 'Data DP tidak ditemukan',
        'bs' => 'bg-dark',
        'lte' => 'label-default',
      ],
      default => [
        'teks' => 'Status tidak dikenal',
        'bs' => 'bg-dark',
        'lte' => 'label-default',
      ],
    };
  }

  // Aturan tunggal: kapan penyewa boleh mengunggah (atau mengunggah ulang).
  // Dipakai oleh View dan oleh simpanBuktiDp() pada baris yang sudah dikunci.
  public static function bolehUnggah(
    ?string $sttus,
    $ftByr,
    ?string $statusSewa
  ): bool {
    if ($statusSewa !== self::STATUS_SEWA_DIAJUKAN) {
      return false;
    }

    $kunci = self::kunciStatus($sttus, $ftByr);

    return $kunci === 'belum_bayar' || $kunci === 'ditolak';
  }

  // Aturan tunggal: kapan admin boleh memutuskan verifikasi.
  public static function bolehDiputuskan(
    ?string $sttus,
    $ftByr,
    ?string $statusSewa
  ): bool {
    return $statusSewa === self::STATUS_SEWA_DIAJUKAN
      && self::kunciStatus($sttus, $ftByr) === 'menunggu_verifikasi';
  }

  // ---------------------------------------------------------------------
  // Pembantu query prepared statement
  // ---------------------------------------------------------------------

  private function siapkan(
    string $sql,
    string $tipe,
    array $param,
    string $konteks
  ): mysqli_stmt {
    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
      throw new RuntimeException($konteks . ' gagal disiapkan.');
    }

    if ($tipe !== '') {
      $stmt->bind_param($tipe, ...$param);
    }

    if (!$stmt->execute()) {
      $stmt->close();
      throw new RuntimeException($konteks . ' gagal dijalankan.');
    }

    return $stmt;
  }

  private function ambilSatu(
    string $sql,
    string $tipe,
    array $param,
    string $konteks
  ): ?array {
    $stmt = $this->siapkan($sql, $tipe, $param, $konteks);
    $baris = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return is_array($baris) ? $baris : null;
  }

  private function ambilBanyak(
    string $sql,
    string $tipe,
    array $param,
    string $konteks
  ): array {
    $stmt = $this->siapkan($sql, $tipe, $param, $konteks);
    $baris = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $baris;
  }

  // ---------------------------------------------------------------------
  // Pemeriksaan skema: mengubah asumsi menjadi fakta yang divalidasi
  // ---------------------------------------------------------------------

  private function periksaSkema(): void
  {
    if (self::$skemaSudahDiperiksa) {
      return;
    }

    $hasil = $this->db->query('SHOW COLUMNS FROM t_bayar');

    if (!$hasil) {
      throw new RuntimeException('Struktur tabel t_bayar tidak dapat dibaca.');
    }

    $kolom = [];

    while ($baris = $hasil->fetch_assoc()) {
      $kolom[(string) $baris['Field']] = $baris;
    }

    $hasil->free();

    foreach (self::KOLOM_BAYAR as $nama) {
      if (!isset($kolom[$nama])) {
        throw new LogicException(
          'Kolom t_bayar.' . $nama . ' tidak ditemukan pada basis data.'
        );
      }
    }

    $tipeStatus = (string) $kolom['sttus_byr']['Type'];

    if (preg_match('/^enum\((.*)\)$/i', $tipeStatus, $cocok) === 1) {
      $tersedia = str_getcsv($cocok[1], ',', "'", '');

      foreach (
        [
          self::STATUS_PENDING,
          self::STATUS_TERVERIFIKASI,
          self::STATUS_DITOLAK,
        ] as $nilai
      ) {
        if (!in_array($nilai, $tersedia, true)) {
          throw new LogicException(
            "Nilai status '" . $nilai . "' tidak ada pada ENUM "
              . 't_bayar.sttus_byr. Nilai yang tersedia: '
              . implode(', ', $tersedia)
              . '. Sesuaikan konstanta STATUS_* pada Pembayaran_model.'
          );
        }
      }
    }

    $tipeBerkas = (string) $kolom['ft_byr']['Type'];

    if (
      preg_match('/^varchar\((\d+)\)/i', $tipeBerkas, $cocok) === 1 &&
      (int) $cocok[1] < self::PANJANG_MIN_FT_BYR
    ) {
      throw new LogicException(
        'Kolom t_bayar.ft_byr hanya ' . $cocok[1] . ' karakter; dibutuhkan '
          . 'minimal ' . self::PANJANG_MIN_FT_BYR
          . ' karakter untuk nama berkas bukti.'
      );
    }

    if (strtoupper((string) $kolom['ft_byr']['Null']) !== 'YES') {
      throw new LogicException('Kolom t_bayar.ft_byr harus mengizinkan NULL.');
    }

    self::$skemaSudahDiperiksa = true;
  }

  // ---------------------------------------------------------------------
  // Pembacaan data sisi penyewa (selalu dibatasi oleh pemilik)
  // ---------------------------------------------------------------------

  public function getDaftarTransaksiPenyewa(string $userPlg): array
  {
    return $this->ambilBanyak(
      "SELECT
          s.kode_sewa,
          s.tgl_mulai,
          s.tgl_selesai,
          s.wkt_ambil,
          s.status_sewa,
          b.kode_bayar,
          b.nominal,
          b.sttus_byr,
          b.ft_byr,
          b.tgl_wkt_byr,
          b.tgl_wkt_ver,
          (
            SELECT COALESCE(SUM(p.hrg_sewa * p.n_hari), 0)
            FROM t_pilih p
            WHERE p.kode_sewa = s.kode_sewa
          ) AS total
        FROM t_sewa s
        LEFT JOIN t_bayar b
          ON b.kode_sewa = s.kode_sewa AND b.jns_byr = 'dp'
        WHERE s.user_plg = ?
          AND s.status_sewa <> 'keranjang'
        ORDER BY s.kode_sewa DESC",
      's',
      [$userPlg],
      'Pengambilan daftar transaksi'
    );
  }

  public function getDetailMilik(string $kodeSewa, string $userPlg): ?array
  {
    $header = $this->ambilSatu(
      "SELECT
          s.kode_sewa,
          s.tgl_mulai,
          s.tgl_selesai,
          s.wkt_ambil,
          s.status_sewa,
          s.user_plg,
          b.kode_bayar,
          b.tgl_wkt_byr,
          b.ft_byr,
          b.mtode_byr,
          b.nominal,
          b.sttus_byr,
          b.tgl_wkt_ver
        FROM t_sewa s
        LEFT JOIN t_bayar b
          ON b.kode_sewa = s.kode_sewa AND b.jns_byr = 'dp'
        WHERE s.kode_sewa = ?
          AND s.user_plg = ?
          AND s.status_sewa <> 'keranjang'
        ORDER BY b.kode_bayar ASC
        LIMIT 1",
      'ss',
      [$kodeSewa, $userPlg],
      'Pengambilan detail pembayaran'
    );

    return $header === null ? null : $this->lengkapi($header);
  }

  // ---------------------------------------------------------------------
  // Pembacaan data sisi admin
  // ---------------------------------------------------------------------

  public function getDaftarVerifikasi(): array
  {
    return $this->ambilBanyak(
      "SELECT
          b.kode_bayar,
          b.kode_sewa,
          b.nominal,
          b.sttus_byr,
          b.ft_byr,
          b.tgl_wkt_byr,
          b.tgl_wkt_ver,
          s.status_sewa,
          s.user_plg,
          pe.nm_lngkp
        FROM t_bayar b
        INNER JOIN t_sewa s ON s.kode_sewa = b.kode_sewa
        LEFT JOIN t_penyewa pe ON pe.user_plg = s.user_plg
        WHERE b.jns_byr = 'dp'
        ORDER BY
          (b.sttus_byr = 'pending' AND b.ft_byr IS NOT NULL) DESC,
          b.tgl_wkt_byr DESC,
          b.kode_bayar DESC",
      '',
      [],
      'Pengambilan daftar verifikasi'
    );
  }

  public function getDetailByKodeBayar(string $kodeBayar): ?array
  {
    $header = $this->ambilSatu(
      "SELECT
          s.kode_sewa,
          s.tgl_mulai,
          s.tgl_selesai,
          s.wkt_ambil,
          s.status_sewa,
          s.user_plg,
          pe.nm_lngkp,
          pe.no_hp,
          b.kode_bayar,
          b.tgl_wkt_byr,
          b.ft_byr,
          b.mtode_byr,
          b.nominal,
          b.sttus_byr,
          b.tgl_wkt_ver
        FROM t_bayar b
        INNER JOIN t_sewa s ON s.kode_sewa = b.kode_sewa
        LEFT JOIN t_penyewa pe ON pe.user_plg = s.user_plg
        WHERE b.kode_bayar = ?
          AND b.jns_byr = 'dp'
        LIMIT 1",
      's',
      [$kodeBayar],
      'Pengambilan detail verifikasi'
    );

    return $header === null ? null : $this->lengkapi($header);
  }

  // Menambahkan rincian kendaraan, total sewa, dan sisa setelah DP.
  private function lengkapi(array $header): array
  {
    $detail = $this->ambilBanyak(
      "SELECT
          p.no_plat,
          p.hrg_sewa,
          p.n_hari,
          m.nm_mobil
        FROM t_pilih p
        INNER JOIN t_mobil m ON m.no_plat = p.no_plat
        WHERE p.kode_sewa = ?
        ORDER BY m.nm_mobil ASC",
      's',
      [(string) $header['kode_sewa']],
      'Pengambilan rincian kendaraan'
    );

    $total = 0;

    foreach ($detail as $item) {
      $total += (int) $item['hrg_sewa'] * (int) $item['n_hari'];
    }

    $header['detail'] = $detail;
    $header['total'] = $total;
    $header['sisa'] = $total - (int) ($header['nominal'] ?? 0);

    return $header;
  }

  // ---------------------------------------------------------------------
  // Penyewa: unggah bukti. UPDATE record DP yang sudah ada (tanpa INSERT).
  // Mengembalikan nama berkas lama (atau null) agar Controller menghapusnya
  // setelah commit berhasil.
  // ---------------------------------------------------------------------

  public function simpanBuktiDp(
    string $kodeSewa,
    string $userPlg,
    string $namaBerkas
  ): ?string {
    $kodeSewa = trim($kodeSewa);
    $userPlg = trim($userPlg);

    if ($kodeSewa === '' || $userPlg === '' || $namaBerkas === '') {
      throw new InvalidArgumentException('Data pembayaran tidak lengkap.');
    }

    $this->periksaSkema();
    $this->db->begin_transaction();

    try {
      // Kunci baris DP lebih dulu (urutan kunci sama dengan putuskan()).
      $dp = $this->ambilSatu(
        "SELECT kode_bayar, ft_byr, sttus_byr
          FROM t_bayar
          WHERE kode_sewa = ? AND jns_byr = 'dp'
          ORDER BY kode_bayar ASC
          LIMIT 1
          FOR UPDATE",
        's',
        [$kodeSewa],
        'Penguncian pembayaran DP'
      );

      $sewa = $this->ambilSatu(
        'SELECT user_plg, status_sewa
          FROM t_sewa
          WHERE kode_sewa = ?
          LIMIT 1',
        's',
        [$kodeSewa],
        'Pemeriksaan kepemilikan transaksi'
      );

      // Pesan sama untuk "tidak ada" dan "bukan milik" agar tidak membocorkan
      // keberadaan transaksi milik penyewa lain.
      if ($sewa === null || (string) $sewa['user_plg'] !== $userPlg) {
        throw new InvalidArgumentException(
          'Transaksi tidak ditemukan atau bukan milik akun ini.'
        );
      }

      if ($dp === null) {
        throw new InvalidArgumentException(
          'Record pembayaran DP untuk transaksi ini tidak ditemukan. '
            . 'Hubungi admin.'
        );
      }

      if (
        !self::bolehUnggah(
          $dp['sttus_byr'],
          $dp['ft_byr'],
          $sewa['status_sewa']
        )
      ) {
        $kunci = self::kunciStatus($dp['sttus_byr'], $dp['ft_byr']);

        throw new InvalidArgumentException(match ($kunci) {
          'terverifikasi' => 'Pembayaran DP sudah terverifikasi.',
          'menunggu_verifikasi' => 'Bukti pembayaran sedang menunggu verifikasi admin.',
          default => 'Transaksi ini tidak dapat menerima pembayaran.',
        });
      }

      $berkasLama = ($dp['ft_byr'] === null || $dp['ft_byr'] === '')
        ? null
        : (string) $dp['ft_byr'];

      $stmt = $this->siapkan(
        "UPDATE t_bayar
          SET ft_byr = ?,
              tgl_wkt_byr = NOW(),
              sttus_byr = ?,
              tgl_wkt_ver = NULL
          WHERE kode_bayar = ?
            AND kode_sewa = ?
            AND jns_byr = 'dp'
            AND (
              (sttus_byr = ? AND ft_byr IS NULL)
              OR sttus_byr = ?
            )",
        'ssssss',
        [
          $namaBerkas,
          self::STATUS_PENDING,
          (string) $dp['kode_bayar'],
          $kodeSewa,
          self::STATUS_PENDING,
          self::STATUS_DITOLAK,
        ],
        'Penyimpanan bukti pembayaran'
      );

      $berubah = $stmt->affected_rows === 1;
      $stmt->close();

      if (!$berubah) {
        throw new RuntimeException('Record pembayaran DP tidak berubah.');
      }

      $this->db->commit();

      return $berkasLama;
    } catch (Throwable $e) {
      $this->db->rollback();
      throw $e;
    }
  }

  // ---------------------------------------------------------------------
  // Admin: putusan verifikasi (setuju / tolak)
  // ---------------------------------------------------------------------

  public function verifikasi(string $kodeBayar): void
  {
    $this->putuskan($kodeBayar, self::STATUS_TERVERIFIKASI);
  }

  public function tolak(string $kodeBayar): void
  {
    $this->putuskan($kodeBayar, self::STATUS_DITOLAK);
  }

  private function putuskan(string $kodeBayar, string $statusBaru): void
  {
    $kodeBayar = trim($kodeBayar);

    if ($kodeBayar === '') {
      throw new InvalidArgumentException('Kode pembayaran wajib diisi.');
    }

    if (
      $statusBaru !== self::STATUS_TERVERIFIKASI &&
      $statusBaru !== self::STATUS_DITOLAK
    ) {
      throw new LogicException('Status putusan tidak valid.');
    }

    $this->periksaSkema();
    $this->db->begin_transaction();

    try {
      $dp = $this->ambilSatu(
        "SELECT kode_bayar, kode_sewa, ft_byr, sttus_byr
          FROM t_bayar
          WHERE kode_bayar = ? AND jns_byr = 'dp'
          LIMIT 1
          FOR UPDATE",
        's',
        [$kodeBayar],
        'Penguncian pembayaran DP'
      );

      if ($dp === null) {
        throw new InvalidArgumentException('Data pembayaran DP tidak ditemukan.');
      }

      $sewa = $this->ambilSatu(
        'SELECT status_sewa FROM t_sewa WHERE kode_sewa = ? LIMIT 1',
        's',
        [(string) $dp['kode_sewa']],
        'Pemeriksaan status transaksi'
      );

      if (
        $sewa === null ||
        !self::bolehDiputuskan(
          $dp['sttus_byr'],
          $dp['ft_byr'],
          $sewa['status_sewa']
        )
      ) {
        throw new InvalidArgumentException(
          'Pembayaran ini tidak sedang menunggu verifikasi. '
            . 'Muat ulang halaman untuk melihat status terbaru.'
        );
      }

      $stmt = $this->siapkan(
        "UPDATE t_bayar
          SET sttus_byr = ?,
              tgl_wkt_ver = NOW()
          WHERE kode_bayar = ?
            AND jns_byr = 'dp'
            AND sttus_byr = ?
            AND ft_byr IS NOT NULL",
        'sss',
        [$statusBaru, (string) $dp['kode_bayar'], self::STATUS_PENDING],
        'Penyimpanan putusan verifikasi'
      );

      $berubah = $stmt->affected_rows === 1;
      $stmt->close();

      if (!$berubah) {
        throw new RuntimeException('Putusan verifikasi tidak tersimpan.');
      }

      $this->db->commit();
    } catch (Throwable $e) {
      $this->db->rollback();
      throw $e;
    }
  }
}
