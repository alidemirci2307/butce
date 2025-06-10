<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

$expenses = readData('data/expenses.json');
$budgets = readData('data/budget.json');

// Rapor parametreleri
$reportYear = $_GET['year'] ?? date('Y');
$reportMonth = $_GET['month'] ?? date('m');

// Aylık rapor oluştur
$monthlyReport = generateMonthlyReport($expenses, $reportMonth, $reportYear);
$monthlyBudget = $budgets[$reportYear][$reportMonth] ?? 0;

// Kategorilere göre harcama verileri (Chart.js için)
$categories = array_keys($monthlyReport['by_category']);
$categoryAmounts = array_values($monthlyReport['by_category']);

// Kullanıcılara göre harcama verileri
$users = ['Ali', 'Sena', 'Ortak'];
$userAmounts = [
    $monthlyReport['by_user']['Ali'],
    $monthlyReport['by_user']['Sena'],
    $monthlyReport['by_user']['Ortak']
];
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Harcama Raporları</h1>
    
    <!-- Rapor Filtreleme -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Rapor Filtrele</h5>
        </div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-6">
                    <label for="year" class="form-label">Yıl</label>
                    <select class="form-select" id="year" name="year">
                        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == $reportYear ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="month" class="form-label">Ay</label>
                    <select class="form-select" id="month" name="month">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= sprintf('%02d', $m) ?>" <?= $m == $reportMonth ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Raporu Göster</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Özet Bilgiler -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Toplam Harcama</h5>
                    <p class="card-text h4"><?= number_format($monthlyReport['total'], 2) ?> TL</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-<?= $monthlyReport['total'] <= $monthlyBudget ? 'success' : 'danger' ?>">
                <div class="card-body">
                    <h5 class="card-title">Bütçe</h5>
                    <p class="card-text h4"><?= number_format($monthlyBudget, 2) ?> TL</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Kalan</h5>
                    <p class="card-text h4"><?= number_format($monthlyBudget - $monthlyReport['total'], 2) ?> TL</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafikler -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Kategorilere Göre Harcamalar</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Kullanıcılara Göre Harcamalar</h5>
                </div>
                <div class="card-body">
                    <canvas id="userChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Kategori grafiği
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
            data: <?= json_encode($categoryAmounts) ?>,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
            ]
        }]
    }
});

// Kullanıcı grafiği
const userCtx = document.getElementById('userChart').getContext('2d');
const userChart = new Chart(userCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($users) ?>,
        datasets: [{
            data: <?= json_encode($userAmounts) ?>,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56'
            ]
        }]
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>