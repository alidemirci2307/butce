<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

$expenses = readData('data/expenses.json');
$budgets = readData('data/budget.json');

// Filtreleme parametreleri
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$userFilter = $_GET['user'] ?? null;

// Filtrelenmiş harcamalar
$filteredExpenses = filterExpenses($expenses, $startDate, $endDate, $userFilter);

// Mevcut ay ve yıl
$currentMonth = date('m');
$currentYear = date('Y');
$monthlyReport = generateMonthlyReport($expenses, $currentMonth, $currentYear);
$monthlyBudget = $budgets[$currentYear][$currentMonth] ?? 0;
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Harcama Takip Sistemi</h1>
    
    <!-- Filtreleme Formu -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filtrele</h5>
        </div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Bitiş Tarihi</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>">
                </div>
                <div class="col-md-4">
                    <label for="user" class="form-label">Kullanıcı</label>
                    <select class="form-select" id="user" name="user">
                        <option value="">Hepsi</option>
                        <option value="Ali" <?= $userFilter == 'Ali' ? 'selected' : '' ?>>Ali</option>
                        <option value="Sena" <?= $userFilter == 'Sena' ? 'selected' : '' ?>>Sena</option>
                        <option value="Ortak" <?= $userFilter == 'Ortak' ? 'selected' : '' ?>>Ortak</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filtrele</button>
                    <a href="index.php" class="btn btn-secondary">Sıfırla</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Aylık Özet -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><?= getTurkishMonth(date('Y-m-d')) ?> Özeti</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Toplam Harcama</h5>
                            <p class="card-text h4"><?= number_format($monthlyReport['total'], 2) ?> TL</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-<?= $monthlyReport['total'] <= $monthlyBudget ? 'success' : 'danger' ?> mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Bütçe</h5>
                            <p class="card-text h4"><?= number_format($monthlyBudget, 2) ?> TL</p>
                            <p class="card-text"><?= $monthlyBudget > 0 ? round(($monthlyReport['total'] / $monthlyBudget) * 100) : '0' ?>%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Kalan</h5>
                            <p class="card-text h4"><?= number_format($monthlyBudget - $monthlyReport['total'], 2) ?> TL</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Harcama Listesi -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Harcamalar</h5>
            <a href="add.php" class="btn btn-success">Yeni Harcama Ekle</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Açıklama</th>
                            <th>Kategori</th>
                            <th>Kullanıcı</th>
                            <th>Tutar (TL)</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filteredExpenses as $expense): ?>
                        <tr>
                            <td><?= date('d.m.Y', strtotime($expense['date'])) ?></td>
                            <td><?= htmlspecialchars($expense['description']) ?></td>
                            <td><?= htmlspecialchars($expense['category']) ?></td>
                            <td><?= $expense['user'] ?></td>
                            <td><?= number_format($expense['amount'], 2) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $expense['id'] ?>" class="btn btn-sm btn-warning">Düzenle</a>
                                <a href="delete.php?id=<?= $expense['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu harcamayı silmek istediğinize emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($filteredExpenses)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Kayıtlı harcama bulunamadı</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>