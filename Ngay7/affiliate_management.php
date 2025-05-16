<?php
session_start();

// Lớp cơ bản cho cộng tác viên thường
class AffiliatePartner {
    const PLATFORM_NAME = "VietLink Affiliate";

    private string $name;
    private string $email;
    private float $commissionRate;
    private bool $isActive;

    public function __construct(string $name, string $email, float $commissionRate, bool $isActive = true) {
        $this->name = $name;
        $this->email = $email;
        $this->commissionRate = $commissionRate;
        $this->isActive = $isActive;
    }

    public function __destruct() {
        echo "Cộng tác viên {$this->name} đã được giải phóng khỏi bộ nhớ.<br>";
    }

    public function calculateCommission(float $orderValue): float {
        return ($this->commissionRate / 100) * $orderValue;
    }

    public function getSummary(): string {
        $status = $this->isActive ? "Hoạt động" : "Không hoạt động";
        return "Tên: {$this->name}<br>Email: {$this->email}<br>Tỷ lệ hoa hồng: {$this->commissionRate}%<br>Trạng thái: {$status}<br>Nền tảng: " . self::PLATFORM_NAME . "<br>";
    }

    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getCommissionRate(): float { return $this->commissionRate; }
}

// Lớp kế thừa cho cộng tác viên cao cấp
class PremiumAffiliatePartner extends AffiliatePartner {
    private float $bonusPerOrder;

    public function __construct(string $name, string $email, float $commissionRate, float $bonusPerOrder, bool $isActive = true) {
        parent::__construct($name, $email, $commissionRate, $isActive);
        $this->bonusPerOrder = $bonusPerOrder;
    }

    public function calculateCommission(float $orderValue): float {
        $baseCommission = parent::calculateCommission($orderValue);
        return $baseCommission + $this->bonusPerOrder;
    }

    public function getBonusPerOrder(): float { return $this->bonusPerOrder; }
}

// Lớp quản lý cộng tác viên
class AffiliateManager {
    private array $partners = [];

    public function addPartner(AffiliatePartner $affiliate): void {
        $this->partners[] = $affiliate;
    }

    public function listPartners(): array {
        return $this->partners;
    }

    public function totalCommission(float $orderValue): float {
        $total = 0.0;
        foreach ($this->partners as $partner) {
            $total += $partner->calculateCommission($orderValue);
        }
        return $total;
    }
}

// Khởi tạo $manager và khôi phục từ session
$manager = new AffiliateManager();

// Khôi phục danh sách từ session nếu có
if (isset($_SESSION['partners'])) {
    foreach ($_SESSION['partners'] as $partnerData) {
        if ($partnerData['isPremium']) {
            $manager->addPartner(new PremiumAffiliatePartner($partnerData['name'], $partnerData['email'], $partnerData['commissionRate'], $partnerData['bonusPerOrder']));
        } else {
            $manager->addPartner(new AffiliatePartner($partnerData['name'], $partnerData['email'], $partnerData['commissionRate']));
        }
    }
}

// Thêm 3 cộng tác viên mặc định
$manager->addPartner(new AffiliatePartner("Nguyen Van A", "a@example.com", 5.0));
$manager->addPartner(new AffiliatePartner("Tran Thi B", "b@example.com", 3.0));
$manager->addPartner(new PremiumAffiliatePartner("Le Van C", "c@example.com", 7.0, 50000));
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_partner'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $commissionRate = filter_input(INPUT_POST, 'commission_rate', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0, 'max_range' => 100]]);
    $isPremium = isset($_POST['is_premium']);
    $bonusPerOrder = $isPremium ? filter_input(INPUT_POST, 'bonus_per_order', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]) : 0;

    if ($name && $email && $commissionRate !== false) {
        if ($isPremium && $bonusPerOrder !== false) {
            $partner = new PremiumAffiliatePartner($name, $email, $commissionRate, $bonusPerOrder);
        } else {
            $partner = new AffiliatePartner($name, $email, $commissionRate);
        }
        $manager->addPartner($partner);
        $messages[] = "Đã thêm cộng tác viên {$name} thành công!";

        // Lưu vào session
        $partnerData = [
            'name' => $name,
            'email' => $email,
            'commissionRate' => $commissionRate,
            'isPremium' => $isPremium,
            'bonusPerOrder' => $bonusPerOrder
        ];
        $_SESSION['partners'][] = $partnerData;
    } else {
        $messages[] = "Dữ liệu không hợp lệ. Vui lòng kiểm tra lại!";
    }
}

$orderValue = 2000000; // Giá trị đơn hàng mặc định
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Cộng tác viên - VietLink Affiliate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 900px;
            margin-top: 30px;
        }
        .header-title {
            color: #2c3e50;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-custom {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card-custom:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .form-label {
            font-weight: 500;
            color: #34495e;
        }
        .btn-primary {
            background-color: #3498db;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .table-custom {
            margin-top: 30px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #3498db;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #ecf0f1;
        }
        .table-hover tbody tr:hover {
            background-color: #dfe6e9;
        }
        .table-success {
            background-color: #2ecc71;
            color: white;
        }
        .alert-custom {
            border-left: 5px solid #3498db;
            background-color: #e8f4f8;
        }
        .summary-cell {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="header-title text-center">Quản lý Cộng tác viên - VietLink Affiliate</h1>

        <!-- Hiển thị thông báo -->
        <?php if ($messages): ?>
            <div class="alert alert-custom alert-dismissible fade show" role="alert">
                <?php echo implode('<br>', $messages); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Form thêm cộng tác viên -->
        <div class="card card-custom">
            <div class="card-header">Thêm Cộng tác viên</div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-4">
                        <label for="commission_rate" class="form-label">Tỷ lệ hoa hồng (%)</label>
                        <input type="number" step="0.1" class="form-control" id="commission_rate" name="commission_rate" min="0" max="100" required>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="is_premium" name="is_premium">
                            <label class="form-check-label" for="is_premium">Cộng tác viên cao cấp</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="bonus_per_order" class="form-label">Thưởng mỗi đơn hàng (VNĐ)</label>
                        <input type="number" class="form-control" id="bonus_per_order" name="bonus_per_order" min="0" disabled>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_partner" class="btn btn-primary">Thêm cộng tác viên</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách cộng tác viên -->
        <div class="table-custom">
            <h2 class="mt-4">Danh sách Cộng tác viên</h2>
            <?php
            $partners = $manager->listPartners();
            if (empty($partners)): ?>
                <div class="alert alert-warning" role="alert">
                    Chưa có cộng tác viên nào trong hệ thống.
                </div>
            <?php else: ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Tỷ lệ hoa hồng (%)</th>
                            <th>Thưởng (VNĐ)</th>
                            <th>Hoa hồng (VNĐ)</th>
                            <th>Tóm tắt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($partners as $index => $partner) {
                            $commission = $partner->calculateCommission($orderValue);
                            $bonus = ($partner instanceof PremiumAffiliatePartner) ? $partner->getBonusPerOrder() : 0;
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($partner->getName()) . "</td>";
                            echo "<td>" . htmlspecialchars($partner->getEmail()) . "</td>";
                            echo "<td>" . htmlspecialchars(number_format($partner->getCommissionRate(), 1)) . "%</td>";
                            echo "<td>" . number_format($bonus) . "</td>";
                            echo "<td>" . number_format($commission) . "</td>";
                            echo "<td class='summary-cell'>" . $partner->getSummary() . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot class="table-success">
                        <tr>
                            <td colspan="5" class="text-end"><strong>Tổng hoa hồng</strong></td>
                            <td><strong><?php echo number_format($manager->totalCommission($orderValue)); ?> VNĐ</strong></td>
                        </tr>
                    </tfoot>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('is_premium').addEventListener('change', function() {
            document.getElementById('bonus_per_order').disabled = !this.checked;
        });
    </script>
</body>
</html>