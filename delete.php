<?php
require_once 'includes/functions.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = 'Silinecek harcama belirtilmedi!';
    header('Location: index.php');
    exit;
}

$expenses = readData('data/expenses.json');

// Harcamayı bul ve sil
$found = false;
foreach ($expenses as $key => $expense) {
    if ($expense['id'] == $id) {
        unset($expenses[$key]);
        $found = true;
        break;
    }
}

if ($found) {
    writeData('data/expenses.json', $expenses);
    $_SESSION['message'] = 'Harcama başarıyla silindi!';
} else {
    $_SESSION['error'] = 'Harcama bulunamadı!';
}

header('Location: index.php');
exit;
?>