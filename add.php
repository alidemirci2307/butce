<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expenses = readData('data/expenses.json');
    
    $newExpense = [
        'id' => uniqid(),
        'date' => $_POST['date'],
        'description' => $_POST['description'],
        'category' => $_POST['category'],
        'user' => $_POST['user'],
        'amount' => floatval($_POST['amount'])
    ];
    
    $expenses[] = $newExpense;
    writeData('data/expenses.json', $expenses);
    
    $_SESSION['message'] = 'Harcama başarıyla eklendi!';
    header('Location: index.php');
    exit;
}

$categories = ['Gıda', 'Fatura', 'Ulaşım', 'Kira', 'Eğlence', 'Diğer'];
?>

<div class="container mt-4">
    <h1 class="mb-4">Yeni Harcama Ekle</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="date" class="form-label">Tarih</label>
                    <input type="date" class="form-control" id="date" name="date" required value="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <input type="text" class="form-control" id="description" name="description" required>
                </div>
                
                <div class="mb-3">
                    <label for="category" class="form-label">Kategori</label>
                    <select class="form-select" id="category" name="category" required>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category ?>"><?= $category ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="user" class="form-label">Kullanıcı</label>
                    <select class="form-select" id="user" name="user" required>
                        <option value="Ali">Ali</option>
                        <option value="Sena">Sena</option>
                        <option value="Ortak">Ortak</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="amount" class="form-label">Tutar (TL)</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Kaydet</button>
                <a href="index.php" class="btn btn-secondary">İptal</a>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>