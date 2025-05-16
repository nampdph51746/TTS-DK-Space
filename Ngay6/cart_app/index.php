<?php
session_start();

// Custom exception class
class CartException extends Exception {}

// Danh sách sách mẫu
$books = [
    'Clean Code' => 150000,
    'Design Patterns' => 200000,
    'Refactoring' => 180000
];

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Khởi tạo biến mặc định
$email = isset($_COOKIE['customer_email']) ? $_COOKIE['customer_email'] : '';
$phone = '';
$address = '';
$messages = [];

// Xử lý form khi gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Xác thực đầu vào
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new CartException('Email không hợp lệ.');
        }

        $phone = filter_input(INPUT_POST, 'phone', FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[0-9]{10}$/']
        ]);
        if (!$phone) {
            throw new CartException('Số điện thoại phải là 10 chữ số.');
        }

        // Thay thế FILTER_SANITIZE_STRING bằng FILTER_DEFAULT và làm sạch thủ công
        $address = filter_input(INPUT_POST, 'address', FILTER_DEFAULT);
        $address = htmlspecialchars(strip_tags($address), ENT_QUOTES, 'UTF-8');
        if (empty($address)) {
            throw new CartException('Địa chỉ không được để trống.');
        }

        // Thay thế FILTER_SANITIZE_STRING cho $book
        $book = filter_input(INPUT_POST, 'book', FILTER_DEFAULT);
        $book = htmlspecialchars(strip_tags($book), ENT_QUOTES, 'UTF-8');

        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        // Thêm sách vào giỏ hàng chỉ khi không phải là xác nhận đặt hàng
        if (!isset($_POST['confirm']) && !$book || !isset($books[$book]) || !$quantity) {
            throw new CartException('Sách hoặc số lượng không hợp lệ.');
        }

        // Thêm vào giỏ hàng chỉ khi nhấn "Thêm vào giỏ hàng"
        if (!isset($_POST['confirm']) && $book && isset($books[$book]) && $quantity) {
            if (isset($_SESSION['cart'][$book])) {
                $_SESSION['cart'][$book]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$book] = [
                    'quantity' => $quantity,
                    'price' => $books[$book]
                ];
            }
            $messages[] = 'Đã thêm sách vào giỏ hàng!';
        }

        // Lưu cookie email
        setcookie('customer_email', $email, time() + (7 * 24 * 3600), '/');

        // Xử lý xác nhận đặt hàng
        if (isset($_POST['confirm'])) {
            if (empty($_SESSION['cart'])) {
                throw new CartException('Giỏ hàng trống, không thể xác nhận.');
            }

            $total_amount = 0;
            $cart_data = [
                'customer_email' => $email,
                'products' => [],
                'total_amount' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            foreach ($_SESSION['cart'] as $title => $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $total_amount += $subtotal;
                $cart_data['products'][] = [
                    'title' => $title,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }
            $cart_data['total_amount'] = $total_amount;

            // Lưu vào file JSON
            try {
                $json_data = json_encode($cart_data, JSON_PRETTY_PRINT);
                if (file_put_contents('cart_data.json', $json_data) === false) {
                    throw new CartException('Lỗi khi ghi file JSON.');
                }
                $messages[] = 'Đơn hàng đã được xác nhận và lưu!';
            } catch (Exception $e) {
                // Ghi log lỗi
                file_put_contents('log.txt', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                throw new CartException('Không thể lưu đơn hàng: ' . $e->getMessage());
            }
        }
    } catch (CartException $e) {
        $messages[] = 'Lỗi: ' . $e->getMessage();
    } catch (Exception $e) {
        $messages[] = 'Lỗi hệ thống: ' . $e->getMessage();
        file_put_contents('log.txt', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
}

// Xử lý xóa giỏ hàng
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    if (file_exists('cart_data.json')) {
        unlink('cart_data.json');
    }
    $messages[] = 'Giỏ hàng đã được xóa!';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Giỏ Hàng Sách</h1>

        <!-- Hiển thị thông báo -->
        <?php if ($messages): ?>
            <div class="alert alert-info">
                <?php echo implode('<br>', $messages); ?>
            </div>
        <?php endif; ?>

        <!-- Form thêm sách -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ giao hàng</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
            </div>
            <div class="mb-3">
                <label for="book" class="form-label">Chọn sách</label>
                <select class="form-select" id="book" name="book" required>
                    <?php foreach ($books as $title => $price): ?>
                        <option value="<?php echo htmlspecialchars($title); ?>"><?php echo htmlspecialchars($title); ?> (<?php echo number_format($price); ?> VNĐ)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Số lượng</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
            </div>
            <button type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
            <button type="submit" name="confirm" class="btn btn-success">Xác nhận đặt hàng</button>
            <button type="submit" name="clear_cart" class="btn btn-danger">Xóa giỏ hàng</button>
        </form>

        <!-- Hiển thị giỏ hàng -->
        <?php if (!empty($_SESSION['cart'])): ?>
            <h2>Giỏ hàng hiện tại</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên sách</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $title => $item): ?>
                        <?php $subtotal = $item['quantity'] * $item['price']; $total += $subtotal; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($title); ?></td>
                            <td><?php echo number_format($item['price']); ?> VNĐ</td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($subtotal); ?> VNĐ</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Tổng tiền</strong></td>
                        <td><strong><?php echo number_format($total); ?> VNĐ</strong></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Hiển thị thông tin đơn hàng sau khi xác nhận -->
        <?php if (isset($_POST['confirm']) && empty($messages)): ?>
            <h2>Thông tin đơn hàng</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>Thời gian đặt hàng:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>