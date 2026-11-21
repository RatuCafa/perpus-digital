<?php
declare(strict_types=1);

use App\Exceptions\AppException;
use App\Repositories\{LoanRepository, MediaRepository, UserRepository};
use App\Services\{AuthService, CatalogService, LoanService};
use App\Support\{Http, Seeder};

require __DIR__ . '/../src/autoload.php';
session_start();

$dataDir = __DIR__ . '/../data';
$users = new UserRepository("$dataDir/users.json");
$mediaRepo = new MediaRepository("$dataDir/media.json");
$loansRepo = new LoanRepository("$dataDir/loans.json");
Seeder::run($users, $mediaRepo);

$auth = new AuthService($users);
$catalog = new CatalogService($mediaRepo, $loansRepo);
$loanService = new LoanService($loansRepo, $mediaRepo);
$user = $auth->current();

$page = (string) ($_GET['page'] ?? 'catalog');
$allowed = ['catalog', 'login', 'register', 'loans', 'admin', 'admin-form', 'logout'];
if (!in_array($page, $allowed, true)) { $page = 'catalog'; }

// Penanganan form (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Http::checkCsrf();
        $action = (string) ($_POST['action'] ?? '');
        switch ($action) {
            case 'register':
                $auth->register((string) $_POST['name'], (string) $_POST['email'], (string) $_POST['password'], (string) $_POST['confirm']);
                Http::flash('ok', 'Pendaftaran berhasil. Silakan login.');
                Http::redirect('login');
            case 'login':
                $u = $auth->login((string) $_POST['email'], (string) $_POST['password']);
                Http::flash('ok', 'Selamat datang, ' . $u->getName() . '.');
                Http::redirect($u->canManageCatalog() ? 'admin' : 'catalog');
            case 'borrow':
                if (!$user) { Http::redirect('login'); }
                $loan = $loanService->borrow($user, (int) $_POST['media_id'], (int) $_POST['days']);
                Http::flash('ok', 'Berhasil. Biaya sewa ' . Http::rupiah($loan->getFee()) . ', jatuh tempo ' . $loan->getDueAt() . '.');
                Http::redirect('loans');
            case 'return':
                if (!$user) { Http::redirect('login'); }
                $loan = $loanService->giveBack($user, (int) $_POST['loan_id']);
                Http::flash('ok', 'Dikembalikan.' . ($loan->getFine() > 0 ? ' Denda ' . Http::rupiah($loan->getFine()) . '.' : ''));
                Http::redirect('loans');
            case 'media-save':
                if (!$user || !$user->canManageCatalog()) { throw new AppException('Akses ditolak.'); }
                $catalog->save($_POST, (int) ($_POST['id'] ?? 0));
                Http::flash('ok', 'Media disimpan.');
                Http::redirect('admin');
            case 'media-delete':
                if (!$user || !$user->canManageCatalog()) { throw new AppException('Akses ditolak.'); }
                $catalog->delete((int) $_POST['id']);
                Http::flash('ok', 'Media dihapus.');
                Http::redirect('admin');
        }
    } catch (AppException $e) {
        Http::flash('err', $e->getMessage());
        Http::redirect($page, isset($_GET['id']) ? ['id' => (int) $_GET['id']] : []);
    }
}

if ($page === 'logout') {
    $auth->logout();
    session_start();
    Http::flash('ok', 'Anda sudah keluar.');
    Http::redirect('catalog');
}

// Pembatasan akses halaman
if (in_array($page, ['loans'], true) && !$user) { Http::redirect('login'); }
if (in_array($page, ['admin', 'admin-form'], true) && (!$user || !$user->canManageCatalog())) {
    Http::flash('err', 'Halaman khusus admin.');
    Http::redirect('catalog');
}

$flash = Http::pullFlash();
$title = ucfirst(str_replace('-', ' ', $page));

// Data untuk view
$data = [];
switch ($page) {
    case 'catalog':
        $data['items'] = $catalog->search((string) ($_GET['q'] ?? ''), (string) ($_GET['type'] ?? ''));
        break;
    case 'loans':
        $data['loans'] = $loanService->history($user);
        break;
    case 'admin':
        $data['items'] = $catalog->search();
        $data['loans'] = $loanService->history();
        $data['userNames'] = [];
        foreach ($users->all() as $u) { $data['userNames'][$u->getId()] = $u->getName(); }
        break;
    case 'admin-form':
        $data['item'] = isset($_GET['id']) ? $catalog->find((int) $_GET['id']) : null;
        break;
}
$mediaLookup = [];
foreach ($mediaRepo->all() as $m) { $mediaLookup[$m->getId()] = $m; }

extract($data);
ob_start();
require __DIR__ . '/../views/' . $page . '.php';
$content = ob_get_clean();
require __DIR__ . '/../views/layout.php';
