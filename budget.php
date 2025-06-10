<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

$budgets = readData('data/budget.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = $_POST['year'];
    $month = $_POST['month'];
    $amount = floatval($_POST['amount']);
    
    if (!isset($budgets[$year])) {
        $budgets[$year] = [];
    }
    
    $budgets[$year][$month] = $amount;
    writeData('data/budget.json', $budgets);
    
    $_SESSION['message'] = 'Bütçe başarıyla kaydedildi!';
    header('Location: budget.php');
    exit;
}

// Mevcut yıl ve ay
$currentYear = date('Y');
$currentMonth = date('m');
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Aylık Bütçe Yönetimi</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Bütçe Belirle</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="year" class="form-label">Yıl</label>
                            <select class="form-select" id="year" name="year" required>
                                <?php for ($y = date('Y'); $y <= date('Y') + 5; $y++): ?>
                                <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="month" class="form-label">Ay</label>
                            <select class="form-select" id="month" name="month" required>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= sprintf('%02d', $m) ?>" <?= $m == $currentMonth ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Bütçe Tutarı (TL)</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Kayıtlı Bütçeler</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Yıl</th>
                                    <th>Ay</th>
                                    <th>Bütçe (TL)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($budgets as $year => $months): ?>
                                    <?php foreach ($months as $month => $amount): ?>
                                    <tr>
                                        <td><?= $year ?></td>
                                        <td><?= date('F', mktime(0, 0, 0, $month, 1)) ?></td>
                                        <td><?= number_format($amount, 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>