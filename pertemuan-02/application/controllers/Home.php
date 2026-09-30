<?php
class Home extends Controller
{
    public function index(): void
    {
        $data = [
            'judul' => 'Fondasi MVC DPWL',
            'pesan' => 'Request telah melewati front controller, Router, Controller, dan View.'
        ];
        $this->view('home/index', $data);
    }

    public function info(string $topik = 'mvc'): void
    {
        $this->view('home/info', ['topik' => $topik]);
    }

    public function pramugari(string $nim = '2522500011'): void
    {
        $data = [
            'title' => 'Detail Pramuugari',
            'nim'   => $nim,
            'nama'  => 'Lini An-Nisa',
            'kelas' => 'SI3A'
        ];
        $this->view('home/pramugari', $data);
    }
}