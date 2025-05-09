<?php
// Định nghĩa hằng số
const COMMISSION_RATE = 0.2; // Tỷ lệ hoa hồng 20%
const VAT_RATE = 0.1; // Thuế VAT 10%

// Dữ liệu đầu vào
$campaignName = "Spring Sale 2025"; // Tên chiến dịch
$totalOrders = 150; 
$productPrice = 99.99; 
$productType = "Thời trang"; 
$campaignEnded = true; // Trạng thái chiến dịch (true: kết thúc, false: đang chạy)

// Danh sách đơn hàng mẫu (ID => giá trị)
$orders = [
    "ID001" => 99.99,
    "ID002" => 49.99,
    "ID003" => 99.99,
    "ID004" => 79.99,
    "ID005" => 99.99
];

// Tính toán doanh thu từ danh sách đơn hàng
$totalRevenueFromOrders = 0;
foreach ($orders as $orderValue) {
    $totalRevenueFromOrders += $orderValue;
}
$finalRevenue = $totalRevenueFromOrders;

// Tính chi phí hoa hồng
$commissionCost = $finalRevenue * COMMISSION_RATE;

// Tính thuế VAT
$vatCost = $finalRevenue * VAT_RATE;

// Tính lợi nhuận
$profit = $finalRevenue - $commissionCost - $vatCost;

// Đánh giá hiệu quả chiến dịch
$campaignStatus = "";
if ($profit > 0) {
    $campaignStatus = "Chiến dịch thành công";
} elseif ($profit == 0) {
    $campaignStatus = "Chiến dịch hòa vốn";
} else {
    $campaignStatus = "Chiến dịch thất bại";
}

// Thông báo dựa trên loại sản phẩm
$productMessage = "";
switch ($productType) {
    case "Thời trang":
        $productMessage = "Sản phẩm Thời trang có doanh thu ổn định";
        break;
    case "Điện tử":
        $productMessage = "Sản phẩm Điện tử có tỷ lệ lợi nhuận cao";
        break;
    case "Gia dụng":
        $productMessage = "Sản phẩm Gia dụng có nhu cầu đều đặn";
        break;
    default:
        $productMessage = "Loại sản phẩm không xác định";
}

// Hiển thị kết quả với HTML
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân tích Chiến dịch Affiliate</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .result { font-size: 16px; margin: 10px; }
    </style>
</head>
<body>
    <h2>PHÂN TÍCH CHIẾN DỊCH AFFILIATE MARKETING</h2>
    <p class="result">Tên chiến dịch: <?php echo $campaignName; ?></p>
    <p class="result">Trạng thái: <?php echo $campaignEnded ? "Kết thúc" : "Đang chạy"; ?></p>
    <p class="result">Tổng doanh thu: <?php echo number_format($finalRevenue, 2); ?> USD</p>
    <p class="result">Chi phí hoa hồng: <?php echo number_format($commissionCost, 2); ?> USD</p>
    <p class="result">Thuế VAT: <?php echo number_format($vatCost, 2); ?> USD</p>
    <p class="result">Lợi nhuận: <?php echo number_format($profit, 2); ?> USD</p>
    <p class="result">Đánh giá hiệu quả: <?php echo $campaignStatus; ?></p>
    <p class="result">Thông tin sản phẩm: <?php echo $productMessage; ?></p>

    <h3>Chi tiết đơn hàng</h3>
    <table>
        <tr>
            <th>Mã đơn hàng</th>
            <th>Giá trị (USD)</th>
        </tr>
        <?php foreach ($orders as $orderId => $orderValue): ?>
            <tr>
                <td><?php echo $orderId; ?></td>
                <td><?php echo number_format($orderValue, 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p class="result">Thông báo: Chiến dịch <?php echo $campaignName; ?> đã <?php echo $campaignEnded ? "kết thúc" : "đang chạy"; ?> với lợi nhuận: <?php echo number_format($profit, 2); ?> USD</p>
</body>
</html>