<?php
// JSON dosyasından veri okuma
function readData($file) {
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    $data = file_get_contents($file);
    return json_decode($data, true);
}

// JSON dosyasına veri yazma
function writeData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Harcamaları filtreleme
function filterExpenses($expenses, $startDate = null, $endDate = null, $user = null) {
    $filtered = [];
    foreach ($expenses as $expense) {
        $date = strtotime($expense['date']);
        $passDate = true;
        $passUser = true;
        
        if ($startDate && $date < strtotime($startDate)) {
            $passDate = false;
        }
        if ($endDate && $date > strtotime($endDate)) {
            $passDate = false;
        }
        if ($user && $expense['user'] != $user) {
            $passUser = false;
        }
        
        if ($passDate && $passUser) {
            $filtered[] = $expense;
        }
    }
    return $filtered;
}

// Aylık rapor oluşturma
function generateMonthlyReport($expenses, $month, $year) {
    $report = [
        'total' => 0,
        'by_user' => ['Ali' => 0, 'Sena' => 0, 'Ortak' => 0],
        'by_category' => []
    ];
    
    foreach ($expenses as $expense) {
        $expenseDate = explode('-', $expense['date']);
        if ($expenseDate[1] == $month && $expenseDate[0] == $year) {
            $report['total'] += $expense['amount'];
            $report['by_user'][$expense['user']] += $expense['amount'];
            
            if (!isset($report['by_category'][$expense['category']])) {
                $report['by_category'][$expense['category']] = 0;
            }
            $report['by_category'][$expense['category']] += $expense['amount'];
        }
    }
    
    return $report;
}

function getTurkishMonth($date) {
    $aylar = array(
        'January' => 'Ocak',
        'February' => 'Şubat',
        'March' => 'Mart',
        'April' => 'Nisan',
        'May' => 'Mayıs',
        'June' => 'Haziran',
        'July' => 'Temmuz',
        'August' => 'Ağustos',
        'September' => 'Eylül',
        'October' => 'Ekim',
        'November' => 'Kasım',
        'December' => 'Aralık'
    );
    
    $ayIngilizce = date('F', strtotime($date));
    return $aylar[$ayIngilizce] . ' ' . date('Y', strtotime($date));
}
?>