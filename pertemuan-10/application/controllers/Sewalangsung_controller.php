<?php

class Sewalangsung_controller extends Controller
{
    private Sewalangsung_model $model;

    private const SESI_TOKEN = 'csrf_sewa_langsung';

    public function __construct()
    {
        require_once APPPATH . 'models/Sewalangsung_model.php';
        require_once APPPATH . 'helpers/auth_helper.php';
        require_once APPPATH . 'helpers/flash_helper.php';
        $this->model = new Sewalangsung_model();
    }

   
    public function index(): void
    {
        require_admin_login();
        $this->wajibMetode(['GET']);

        $this->view('admin/sewa_langsung_view', [
            'username' => $_SESSION['admin_username'],
            'menu_aktif' => 'sewa_langsung',
            'daftar' => $this->model->getAll(),
            'flash' => get_flash_message(),
        ]);
    }

    /* ------------------------------------------------------------------
     * GET  admin/sewa-langsung/tambah : tampilkan formulir
     * POST admin/sewa-langsung/tambah : simpan (header + detail + bayar)
     * ---------------------------------------------------------------- */
    public function tambah(): void
    {
        require_admin_login();
        $this->wajibMetode(['GET', 'POST']);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->renderForm($this->nilaiAwal(), []);
            return;
        }

        // ----- POST -----
        $old = $this->bacaInput();

        if (!$this->tokenValid((string) ($_POST['csrf_token'] ?? ''))) {
            $this->renderForm($old, [
                'Sesi formulir tidak valid atau kedaluwarsa. '
                . 'Silakan kirim ulang.',
            ]);
            return;
        }

        [$data, $errors] = $this->validasiBentuk($old);
        if ($errors) {
            $this->renderForm($old, $errors);
            return;
        }

        try {
            $kode = $this->model->simpan($data);
        } catch (InvalidArgumentException $e) {
            // pelanggaran aturan bisnis -> pesan aman ditampilkan
            $this->renderForm($old, [$e->getMessage()]);
            return;
        } catch (Throwable $e) {
            error_log('Simpan sewa langsung gagal: ' . $e->getMessage());
            $this->renderForm($old, [
                'Transaksi gagal disimpan dan tidak ada data yang berubah. '
                . 'Silakan coba lagi.',
            ]);
            return;
        }

        unset($_SESSION[self::SESI_TOKEN]); // token sekali pakai
        set_flash_message(
            'success',
            'Transaksi sewa langsung ' . $kode . ' berhasil disimpan.'
        );
        // PRG: POST -> redirect -> GET
        header('Location: ' . site_url(
            'admin/sewa-langsung/detail/' . $this->kodeKeUrl($kode)
        ));
        exit;
    }

    /* ------------------------------------------------------------------
     * GET admin/sewa-langsung/detail/{kode~dengan~tilde}
     * ---------------------------------------------------------------- */
    public function detail(string $kode = ''): void
    {
        require_admin_login();
        $this->wajibMetode(['GET']);

        $kodeSewa = $this->kodeDariUrl($kode);
        $transaksi = $kodeSewa === '' ? null : $this->model->getDetail($kodeSewa);
        if ($transaksi === null) {
            set_flash_message('danger', 'Transaksi sewa langsung tidak ditemukan.');
            header('Location: ' . site_url('admin/sewa-langsung'));
            exit;
        }

        $this->view('admin/sewa_langsung_detail', [
            'username' => $_SESSION['admin_username'],
            'menu_aktif' => 'sewa_langsung',
            'trx' => $transaksi,
            'flash' => get_flash_message(),
            'token' => $this->buatToken(),
        ]);
    }

    /* ------------------------------------------------------------------
     * POST admin/sewa-langsung/batalkan/{kode}
     * ---------------------------------------------------------------- */
    public function batalkan(string $kode = ''): void
    {
        require_admin_login();
        $this->wajibMetode(['POST']);

        $kodeSewa = $this->kodeDariUrl($kode);
        $kembali = site_url(
            'admin/sewa-langsung/detail/' . $this->kodeKeUrl($kodeSewa)
        );

        if (!$this->tokenValid((string) ($_POST['csrf_token'] ?? ''))) {
            set_flash_message('danger', 'Sesi tidak valid. Muat ulang halaman.');
            header('Location: ' . $kembali);
            exit;
        }

        try {
            $this->model->batalkan($kodeSewa);
            set_flash_message(
                'success',
                'Transaksi ' . $kodeSewa . ' dibatalkan dan kendaraan '
                . 'dikembalikan ke status tersedia. Pengembalian dana '
                . '(jika ada) diproses manual.'
            );
        } catch (InvalidArgumentException $e) {
            set_flash_message('danger', $e->getMessage());
        } catch (Throwable $e) {
            error_log('Batal sewa langsung gagal: ' . $e->getMessage());
            set_flash_message('danger', 'Pembatalan gagal. Tidak ada data yang berubah.');
        }
        header('Location: ' . $kembali);
        exit;
    }

    /* ==================================================================
     * Pendukung
     * ================================================================ */

    private function renderForm(array $old, array $errors): void
    {
        $mobil = $this->model->getMobilTersedia();
        $this->view('admin/sewa_langsung_form', [
            'username' => $_SESSION['admin_username'],
            'menu_aktif' => 'sewa_langsung',
            'old' => $old,
            'errors' => $errors,
            'penyewa' => $this->model->getPenyewa(),
            'mobil' => $mobil,
            'token' => $this->buatToken(),
            'dp_persen' => Sewalangsung_model::DP_PERSEN,
            'maks_unit' => Sewalangsung_model::MAKS_UNIT,
        ]);
    }

    private function nilaiAwal(): array
    {
        $sekarang = new DateTimeImmutable('now');
        return [
            'user_plg' => '',
            'tgl_mulai' => $sekarang->format('Y-m-d'),
            'tgl_selesai' => $sekarang->modify('+1 day')->format('Y-m-d'),
            'wkt_ambil' => $sekarang->format('H:i'),
            'no_plat' => [],
            'nominal' => '',
        ];
    }

    /** Ambil input mentah (hanya normalisasi tipe, belum divalidasi). */
    private function bacaInput(): array
    {
        $plat = $_POST['no_plat'] ?? [];
        if (!is_array($plat)) {
            $plat = [];
        }
        $plat = array_values(array_filter(
            array_map(
                static fn($v) => is_string($v) ? trim($v) : '',
                $plat
            ),
            static fn($v) => $v !== ''
        ));
        return [
            'user_plg' => trim((string) ($_POST['user_plg'] ?? '')),
            'tgl_mulai' => trim((string) ($_POST['tgl_mulai'] ?? '')),
            'tgl_selesai' => trim((string) ($_POST['tgl_selesai'] ?? '')),
            'wkt_ambil' => trim((string) ($_POST['wkt_ambil'] ?? '')),
            'no_plat' => $plat,
            'nominal' => trim((string) ($_POST['nominal'] ?? '')),
        ];
    }

    /**
     * Validasi BENTUK input (wajib isi, format, tipe).
     * Aturan bisnis yang butuh DB diputuskan di Model.
     * @return array{0: array, 1: string[]} [data bersih, daftar error]
     */
    private function validasiBentuk(array $in): array
    {
        $errors = [];

        if ($in['user_plg'] === '' || strlen($in['user_plg']) > 15) {
            $errors[] = 'Penyewa wajib dipilih.';
        }

        $tglMulai = $this->tanggalValid($in['tgl_mulai']);
        $tglSelesai = $this->tanggalValid($in['tgl_selesai']);
        if (!$tglMulai) {
            $errors[] = 'Tanggal mulai tidak valid.';
        }
        if (!$tglSelesai) {
            $errors[] = 'Tanggal selesai tidak valid.';
        }

        $waktu = $this->waktuValid($in['wkt_ambil']);
        if ($waktu === null) {
            $errors[] = 'Waktu pengambilan tidak valid.';
        }

        if (count($in['no_plat']) === 0) {
            $errors[] = 'Pilih minimal satu kendaraan.';
        } elseif (count($in['no_plat']) !== count(array_unique($in['no_plat']))) {
            $errors[] = 'Kendaraan yang sama tidak boleh dipilih lebih dari sekali.';
        } else {
            foreach ($in['no_plat'] as $p) {
                if (strlen($p) > 15 || preg_match('/[\x00-\x1F\x7F]/', $p)) {
                    $errors[] = 'Nomor plat kendaraan tidak valid.';
                    break;
                }
            }
        }

        if (
            $in['nominal'] === ''
            || !ctype_digit($in['nominal'])
            || strlen($in['nominal']) > 9
        ) {
            $errors[] = 'Nominal pembayaran harus berupa angka bulat positif.';
        } elseif ((int) $in['nominal'] <= 0) {
            $errors[] = 'Nominal pembayaran harus lebih dari nol.';
        }

        $data = [
            'user_plg' => $in['user_plg'],
            'tgl_mulai' => $in['tgl_mulai'],
            'tgl_selesai' => $in['tgl_selesai'],
            'wkt_ambil' => $waktu ?? '',
            'no_plat' => $in['no_plat'],
            'nominal' => ctype_digit($in['nominal']) ? (int) $in['nominal'] : 0,
        ];
        return [$data, $errors];
    }

    private function tanggalValid(string $tgl): bool
    {
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', $tgl);
        return $d !== false && $d->format('Y-m-d') === $tgl;
    }

    /** Normalisasi ke H:i:s, null jika tidak valid. */
    private function waktuValid(string $waktu): ?string
    {
        foreach (['H:i:s', 'H:i'] as $format) {
            $d = DateTimeImmutable::createFromFormat('!' . $format, $waktu);
            if ($d !== false && $d->format($format) === $waktu) {
                return $d->format('H:i:s');
            }
        }
        return null;
    }

    private function wajibMetode(array $diizinkan): void
    {
        $metode = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        if (!in_array($metode, $diizinkan, true)) {
            http_response_code(405);
            header('Allow: ' . implode(', ', $diizinkan));
            exit('Metode request tidak diizinkan.');
        }
    }

    private function kodeKeUrl(string $kode): string
    {
        return rawurlencode(str_replace('/', '~', $kode));
    }

    private function kodeDariUrl(string $kode): string
    {
        return trim(str_replace('~', '/', $kode));
    }

    /* ----- CSRF ----- */

    private function buatToken(): string
    {
        if (empty($_SESSION[self::SESI_TOKEN])) {
            $_SESSION[self::SESI_TOKEN] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESI_TOKEN];
    }

    private function tokenValid(string $token): bool
    {
        $sesi = (string) ($_SESSION[self::SESI_TOKEN] ?? '');
        return $sesi !== '' && $token !== '' && hash_equals($sesi, $token);
    }
}
