<?php
class Penyewa_admin_controller extends Controller
{
    private Penyewa_model_admin $model;

    public function __construct()
    {
        require_once APPPATH . 'models/Penyewa_admin_model.php';
        require_once APPPATH . 'helpers/auth_helper.php';
        require_once APPPATH . 'helpers/flash_helper.php';
        $this->model = new Penyewa_model_admin();
        if (empty($_SESSION['csrf_penyewa'])) {
            $_SESSION['csrf_penyewa'] = bin2hex(random_bytes(32));
        }
    }

    private function verifyCsrf(): void
    {
        $token = (string) ($_POST['_csrf'] ?? '');
        $stored = (string) ($_SESSION['csrf_penyewa'] ?? '');
        if ($stored === '' || !hash_equals($stored, $token)) {
            http_response_code(403);
            exit('Permintaan tidak valid. Silakan muat ulang halaman dan coba kembali.');
        }
    }

    public function index(): void
    {
        require_admin_login();
        $this->view('admin/penyewa_view', [
            'username' => $_SESSION['admin_username'],
            'penyewa' => $this->model->getAll(),
            'flash' => get_flash_message(),
            'mode' => null, 'penyewa_dipilih' => null, 'error' => null
        ]);
    }

    public function tambah(): void
    {
        require_admin_login();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('tambah', ['user_plg'=>'','nik'=>'','nm_lngkp'=>'','no_hp'=>'','almt_lngkp'=>'','ft_ktp'=>null,'ft_sim_a'=>null]);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Metode request tidak diizinkan.'); }
        $this->verifyCsrf();

        $data = $this->postData();
        $password = (string)($_POST['sandi'] ?? '');
        $error = $this->validate($data, $password, false);
        if ($error !== null) { $this->render('tambah', $data, $error); return; }
        if ($this->model->findByUserPlg($data['user_plg']) !== null) {
            $this->render('tambah', $data, 'Username penyewa sudah terdaftar.'); return;
        }

        $uploads = $this->uploadFiles();
        if (!$uploads['success']) { $this->render('tambah', $data, $uploads['error']); return; }
        $data['ft_ktp'] = $uploads['ft_ktp'];
        $data['ft_sim_a'] = $uploads['ft_sim_a'];
        $data['sandi'] = password_hash($password, PASSWORD_DEFAULT);
        try { $ok = $this->model->create($data); } catch (Throwable $e) { $ok = false; }
        if (!$ok) {
            $this->removeUploads($uploads);
            $this->render('tambah', $data, 'Data penyewa gagal disimpan. Periksa kembali data atau relasi transaksi.');
            return;
        }
        set_flash_message('success', 'Data penyewa berhasil ditambahkan.');
        header('Location: ' . site_url('admin/penyewa')); exit;
    }

    public function ubah(string $userPlg): void
    {
        require_admin_login();
        $userPlg = rawurldecode($userPlg);
        $lama = $this->model->findByUserPlg($userPlg);
        if ($lama === null) {
            set_flash_message('danger', 'Data penyewa tidak ditemukan.');
            header('Location: ' . site_url('admin/penyewa')); exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            unset($lama['sandi']);
            $this->render('ubah', $lama); return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Metode request tidak diizinkan.'); }
        $this->verifyCsrf();

        $data = $this->postData();
        $data['user_plg'] = $userPlg;
        $password = (string)($_POST['sandi'] ?? '');
        $error = $this->validate($data, $password, true);
        if ($error !== null) { $this->render('ubah', array_merge($lama, $data), $error); return; }

        $uploads = $this->uploadFiles();
        if (!$uploads['success']) { $this->render('ubah', array_merge($lama, $data), $uploads['error']); return; }
        $data['ft_ktp'] = $uploads['ft_ktp'] ?? ($lama['ft_ktp'] ?? null);
        $data['ft_sim_a'] = $uploads['ft_sim_a'] ?? ($lama['ft_sim_a'] ?? null);
        $hash = $password === '' ? null : password_hash($password, PASSWORD_DEFAULT);
        try { $ok = $this->model->update($userPlg, $data, $hash); } catch (Throwable $e) { $ok = false; }
        if (!$ok) {
            $this->removeUploads($uploads);
            $this->render('ubah', array_merge($lama, $data), 'Data penyewa gagal diperbarui.');
            return;
        }
        $this->removeOldPhotos($lama, $uploads);
        set_flash_message('success', 'Data penyewa berhasil diperbarui.');
        header('Location: ' . site_url('admin/penyewa')); exit;
    }

    public function hapus(string $userPlg): void
    {
        require_admin_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Metode request tidak diizinkan.'); }
        $this->verifyCsrf();
        $userPlg = rawurldecode($userPlg);
        $lama = $this->model->findByUserPlg($userPlg);
        if ($lama === null) {
            set_flash_message('danger', 'Data penyewa tidak ditemukan.');
        } else {
            try { $ok = $this->model->delete($userPlg); } catch (Throwable $e) { $ok = false; }
            if ($ok) {
                $this->removeOldPhotos($lama, ['ft_ktp'=>null,'ft_sim_a'=>null]);
                set_flash_message('success', 'Data penyewa berhasil dihapus.');
            } else {
                set_flash_message('danger', 'Data penyewa gagal dihapus. Pastikan tidak ada transaksi yang masih terhubung.');
            }
        }
        header('Location: ' . site_url('admin/penyewa')); exit;
    }

    private function postData(): array
    {
        return [
            'user_plg'=>trim((string)($_POST['user_plg'] ?? '')),
            'nik'=>trim((string)($_POST['nik'] ?? '')),
            'nm_lngkp'=>trim((string)($_POST['nm_lngkp'] ?? '')),
            'no_hp'=>trim((string)($_POST['no_hp'] ?? '')),
            'almt_lngkp'=>trim((string)($_POST['almt_lngkp'] ?? '')),
            'ft_ktp'=>null, 'ft_sim_a'=>null
        ];
    }

    private function validate(array $d, string $password, bool $edit): ?string
    {
        if ($d['user_plg']==='' || strlen($d['user_plg'])>30 || preg_match('/[\x00-\x1F\x7F]/',$d['user_plg'])) return 'Username wajib diisi dan maksimal 30 karakter.';
        if (!$edit && ($password==='' || strlen($password)<8 || strlen($password)>72)) return 'Password wajib diisi, minimal 8 dan maksimal 72 byte.';
        if ($password!=='' && (strlen($password)<8 || strlen($password)>72)) return 'Password minimal 8 dan maksimal 72 byte.';
        if (!preg_match('/\A[0-9]{16}\z/',$d['nik'])) return 'NIK wajib terdiri dari tepat 16 digit angka.';
        if ($d['nm_lngkp']==='' || mb_strlen($d['nm_lngkp'])>100) return 'Nama lengkap wajib diisi (maksimal 100 karakter).';
        if ($d['no_hp']==='' || strlen($d['no_hp'])>15 || !preg_match('/\A[0-9+(). -]+\z/',$d['no_hp'])) return 'Nomor HP tidak valid (maksimal 15 karakter).';
        if ($d['almt_lngkp']==='' || mb_strlen($d['almt_lngkp'])>300) return 'Alamat wajib diisi (maksimal 300 karakter).';
        return null;
    }

    private function uploadFiles(): array
    {
        $result = ['success'=>true,'ft_ktp'=>null,'ft_sim_a'=>null];
        $dir = dirname(APPPATH).'/uploads/penyewa/';
        foreach (['ft_ktp','ft_sim_a'] as $field) {
            if (!isset($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) continue;
            $f = $_FILES[$field];
            if (($f['error'] ?? -1)!==UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name']) || $f['size']>5*1024*1024) {
                $this->removeUploads($result); return ['success'=>false,'error'=>'Foto harus berhasil diunggah dan maksimal 5 MB.'];
            }
            $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
            $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
            if ($ext===null || @getimagesize($f['tmp_name'])===false) {
                $this->removeUploads($result); return ['success'=>false,'error'=>'Format foto harus JPEG, PNG, atau WEBP.'];
            }
            if (!is_dir($dir) && !mkdir($dir,0755,true) && !is_dir($dir)) {
                $this->removeUploads($result); return ['success'=>false,'error'=>'Folder upload tidak dapat dibuat.'];
            }
            $name=$field.'_'.date('YmdHis').'_'.bin2hex(random_bytes(8)).'.'.$ext;
            if (!move_uploaded_file($f['tmp_name'],$dir.$name)) {
                $this->removeUploads($result); return ['success'=>false,'error'=>'Foto gagal disimpan.'];
            }
            $result[$field]=$name;
        }
        return $result;
    }

    private function removeUploads(array $files): void
    {
        $dir=dirname(APPPATH).'/uploads/penyewa/';
        foreach (['ft_ktp','ft_sim_a'] as $field) {
            $name=$files[$field] ?? null;
            if (is_string($name) && preg_match('/^'.preg_quote($field,'/').'_\\d{10,14}_[a-f0-9]{12,16}\\.(jpg|png|webp)$/',$name)) {
                if (is_file($dir.$name)) @unlink($dir.$name);
            }
        }
    }

    private function removeOldPhotos(array $old, array $new): void
    {
        foreach (['ft_ktp','ft_sim_a'] as $field) {
            if (!empty($old[$field]) && $old[$field] !== ($new[$field] ?? null)) {
                $this->removeUploads([$field=>$old[$field]]);
            }
        }
    }

    private function render(string $mode, ?array $selected=null, ?string $error=null): void
    {
        $this->view('admin/penyewa_view', [
            'username'=>$_SESSION['admin_username'],
            'penyewa'=>$this->model->getAll(), 'flash'=>null,
            'mode'=>$mode, 'penyewa_dipilih'=>$selected, 'error'=>$error
        ]);
    }
}
