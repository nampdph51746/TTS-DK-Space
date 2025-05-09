<?php
session_start();

// Biến toàn cục để lưu tổng thu/chi và tỷ giá
$GLOBALS['total_income'] = $GLOBALS['total_income'] ?? 0; // Sử dụng $GLOBALS để lưu tổng thu
$GLOBALS['total_expense'] = $GLOBALS['total_expense'] ?? 0; // Sử dụng $GLOBALS để lưu tổng chi
$GLOBALS['exchange_rate'] = 1; // Tỷ giá mặc định (mở rộng đa tiền tệ)

// Khởi tạo mảng giao dịch trong session nếu chưa tồn tại
$_SESSION['transactions'] = $_SESSION['transactions'] ?? [];

$errors = [];
$success = '';

// Xử lý form khi submit (sử dụng $_POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $transaction_name = trim($_POST['transaction_name'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $type = $_POST['type'] ?? '';
    $note = trim($_POST['note'] ?? '');
    $date = trim($_POST['date'] ?? '');

    // Kiểm tra trường bắt buộc trước
    if (empty($transaction_name) || empty($amount) || empty($type) || empty($date)) {
        $errors[] = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
    } else {
        // Xác thực bằng Regular Expressions chỉ khi các trường không trống
        // Kiểm tra tên giao dịch: Chấp nhận chữ cái tiếng Việt, số, và khoảng trắng
        if (!preg_match('/^[\p{L}\p{N}\s]+$/u', $transaction_name)) {
            $errors[] = 'Tên giao dịch không được chứa ký tự đặc biệt.';
        }

        // Kiểm tra số tiền: Chỉ chứa số dương
        if (!preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $amount) || floatval($amount) <= 0) {
            $errors[] = 'Số tiền phải là số dương hợp lệ.';
        }

        // Kiểm tra ngày: Định dạng dd/mm/yyyy (thoát ký tự /)
        if (!preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/([12][0-9]{3})$/', $date)) {
            $errors[] = 'Ngày thực hiện phải đúng định dạng dd/mm/yyyy.';
        }

        // Kiểm tra từ khóa nhạy cảm trong ghi chú
        if (!empty($note) && preg_match('/nợ xấu|vay nóng/i', $note)) {
            $errors[] = 'Ghi chú chứa từ khóa nhạy cảm (nợ xấu, vay nóng).';
        }
    }

    // Nếu không có lỗi, lưu giao dịch vào session
    if (empty($errors)) {
        $amount = floatval($amount);
        $transaction = [
            'name' => $transaction_name,
            'amount' => $amount,
            'type' => $type,
            'note' => $note,
            'date' => $date
        ];
        $_SESSION['transactions'][] = $transaction; // Lưu vào $_SESSION

        // Cập nhật tổng thu/chi (sử dụng $GLOBALS)
        if ($type === 'thu') {
            $GLOBALS['total_income'] += $amount;
        } elseif ($type === 'chi') {
            $GLOBALS['total_expense'] += $amount;
        }

        $success = 'Giao dịch đã được lưu thành công!';
    }
}

// Tính số dư
$balance = $GLOBALS['total_income'] - $GLOBALS['total_expense'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Ứng dụng Quản lý Giao dịch Tài chính</title>
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
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .radio-group {
            margin: 5px 0;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
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
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #e7f3e7;
            border-left: 4px solid #4CAF50;
        }
    </style>
</head>
<body>
    <h2>ỨNG DỤNG QUẢN LÝ GIAO DỊCH TÀI CHÍNH</h2>
    <div class="container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <!-- Sử dụng $_SERVER['PHP_SELF'] để gửi form về chính nó -->
            <div class="form-group">
                <label for="transaction_name">Tên giao dịch:</label>
                <input type="text" id="transaction_name" name="transaction_name" value="<?php echo htmlspecialchars($_POST['transaction_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="amount">Số tiền:</label>
                <input type="number" id="amount" name="amount" step="0.01" value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Loại giao dịch:</label>
                <div class="radio-group">
                    <input type="radio" name="type" value="thu" <?php echo ($_POST['type'] ?? '') === 'thu' ? 'checked' : ''; ?>> Thu
                    <input type="radio" name="type" value="chi" <?php echo ($_POST['type'] ?? '') === 'chi' ? 'checked' : ''; ?>> Chi
                </div>
            </div>
            <div class="form-group">
                <label for="note">Ghi chú:</label>
                <textarea id="note" name="note"><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="date">Ngày thực hiện (dd/mm/yyyy):</label>
                <input type="text" id="date" name="date" value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>" required>
            </div>
            <button type="submit" name="submit">Lưu giao dịch</button>
        </form>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['transactions'])): ?>
            <table>
                <tr>
                    <th>Tên giao dịch</th>
                    <th>Số tiền</th>
                    <th>Loại</th>
                    <th>Ghi chú</th>
                    <th>Ngày</th>
                </tr>
                <?php foreach ($_SESSION['transactions'] as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['name']); ?></td>
                        <td><?php echo number_format($transaction['amount'], 2); ?> VNĐ</td>
                        <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['note']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div class="summary">
                <p>Tổng thu: <?php echo number_format($GLOBALS['total_income'], 2); ?> VNĐ</p>
                <p>Tổng chi: <?php echo number_format($GLOBALS['total_expense'], 2); ?> VNĐ</p>
                <p>Số dư: <?php echo number_format($balance, 2); ?> VNĐ</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>