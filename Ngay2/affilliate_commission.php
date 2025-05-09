<?php
// Định nghĩa dữ liệu mẫu
$users = [
    1 => ['name' => 'Alice', 'referrer_id' => null],
    2 => ['name' => 'Bob', 'referrer_id' => 1],
    3 => ['name' => 'Charlie', 'referrer_id' => 2],
    4 => ['name' => 'David', 'referrer_id' => 3],
    5 => ['name' => 'Eva', 'referrer_id' => 1],
];

$orders = [
    ['order_id' => 101, 'user_id' => 4, 'amount' => 200.0],
    ['order_id' => 102, 'user_id' => 3, 'amount' => 150.0],
    ['order_id' => 103, 'user_id' => 5, 'amount' => 300.0],
];

$commissionRates = [
    1 => 0.10, // Cấp 1: 10%
    2 => 0.05, // Cấp 2: 5%
    3 => 0.02  // Cấp 3: 2%
];

// Biến để lưu log hoa hồng
$commissionLog = [];

// Hàm không tham số để khởi tạo log
function initializeLog(): void {
    global $commissionLog;
    $commissionLog = [];
}

// Hàm với tham số mặc định để tính hoa hồng theo cấp
function calculateCommissionForLevel(float $amount, int $level, float $defaultRate = 0.0): float {
    global $commissionRates;
    $rate = $commissionRates[$level] ?? $defaultRate;
    return $amount * $rate;
}

// Hàm đệ quy để tìm danh sách người giới thiệu
function getReferralChain(int $userId, array $users): array {
    $chain = [];
    $currentId = $userId;
    while ($currentId !== null) {
        if (!isset($users[$currentId])) break;
        $chain[] = $currentId;
        $currentId = $users[$currentId]['referrer_id'];
    }
    return $chain;
}

// Hàm ẩn danh để xử lý từng đơn hàng với tham chiếu log
$processOrder = function (array $order) use ($users, &$commissionLog) {
    $userId = $order['user_id'];
    $amount = $order['amount'];
    $orderId = $order['order_id'];

    $referralChain = getReferralChain($userId, $users);
    $level = 1;

    foreach ($referralChain as $referrerId) {
        if ($level > 3) break; // Giới hạn cấp 3
        $commission = calculateCommissionForLevel($amount, $level);
        $commissionLog[] = [
            'order_id' => $orderId,
            'user_id' => $referrerId,
            'buyer_id' => $userId,
            'level' => $level,
            'amount' => $commission
        ];
        $level++;
    }
};

// Hàm variadic để xử lý nhiều đơn hàng
function processOrders(array &$commissionLog, array ...$orders): void {
    global $processOrder;
    array_walk($orders, $processOrder);
}

// Hàm tổng hợp với type hinting
function calculateCommission(array $orders, array $users, array $commissionRates, array &$commissionLog): array {
    initializeLog();
    processOrders($commissionLog, ...$orders);
    $totalCommissions = [];
    foreach ($commissionLog as $log) {
        $userId = $log['user_id'];
        $totalCommissions[$userId] = ($totalCommissions[$userId] ?? 0) + $log['amount'];
    }
    return ['log' => $commissionLog, 'totals' => $totalCommissions];
}

// Thực thi logic
$result = calculateCommission($orders, $users, $commissionRates, $commissionLog);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Hoa hồng Affiliate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h2>BÁO CÁO HOA HỒNG AFFILIATE</h2>

    <h3>Tổng hoa hồng của từng người dùng</h3>
    <table>
        <tr>
            <th>Tên người dùng</th>
            <th>ID</th>
            <th>Tổng hoa hồng (USD)</th>
        </tr>
        <?php
        foreach ($result['totals'] as $userId => $total) {
            if (isset($users[$userId])) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($users[$userId]['name']) . "</td>";
                echo "<td>" . $userId . "</td>";
                echo "<td>" . number_format($total, 2) . "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>

    <h3>Chi tiết các khoản hoa hồng</h3>
    <table>
        <tr>
            <th>Mã đơn hàng</th>
            <th>Người nhận</th>
            <th>Người mua</th>
            <th>Cấp</th>
            <th>Hoa hồng (USD)</th>
        </tr>
        <?php
        foreach ($result['log'] as $log) {
            if (isset($users[$log['user_id']]) && isset($users[$log['buyer_id']])) {
                echo "<tr>";
                echo "<td>" . $log['order_id'] . "</td>";
                echo "<td>" . htmlspecialchars($users[$log['user_id']]['name']) . "</td>";
                echo "<td>" . htmlspecialchars($users[$log['buyer_id']]['name']) . "</td>";
                echo "<td>" . $log['level'] . "</td>";
                echo "<td>" . number_format($log['amount'], 2) . "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</body>
</html>