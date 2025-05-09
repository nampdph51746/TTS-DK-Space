<?php
// Định nghĩa dữ liệu mẫu
$employees = [
    ['id' => 101, 'name' => 'Nguyễn Văn A', 'base_salary' => 5000000],
    ['id' => 102, 'name' => 'Trần Thị B', 'base_salary' => 6000000],
    ['id' => 103, 'name' => 'Lê Văn C', 'base_salary' => 5500000],
];

$timesheet = [
    101 => ['2025-03-01', '2025-03-02', '2025-03-04', '2025-03-05'],
    102 => ['2025-03-01', '2025-03-03', '2025-03-04'],
    103 => ['2025-03-02', '2025-03-03', '2025-03-04', '2025-03-05', '2025-03-06'],
];

$adjustments = [
    101 => ['allowance' => 500000, 'deduction' => 200000],
    102 => ['allowance' => 300000, 'deduction' => 100000],
    103 => ['allowance' => 400000, 'deduction' => 150000],
];

// Số ngày làm việc tiêu chuẩn trong tháng
const STANDARD_DAYS = 22;

// Hàm làm sạch dữ liệu chấm công (loại bỏ trùng lặp)
function cleanTimesheet(array &$timesheet): void {
    foreach ($timesheet as &$days) {
        $days = array_unique($days); 
    }
}

// Hàm tính số ngày công thực tế
function calculateWorkingDays(array $timesheet): array {
    $workingDays = [];
    foreach (array_keys($timesheet) as $id) {
        $workingDays[$id] = count($timesheet[$id] ?? []); 
    }
    return $workingDays; 
}

// Hàm tính lương thực lĩnh
function calculateNetSalary(array $employee, int $workingDays, array $adjustments): float {
    $dailySalary = $employee['base_salary'] / STANDARD_DAYS;
    $salary = $dailySalary * $workingDays;
    $allowance = $adjustments[$employee['id']]['allowance'] ?? 0;
    $deduction = $adjustments[$employee['id']]['deduction'] ?? 0;
    return round($salary + $allowance - $deduction); 
}

// Hàm tạo báo cáo lương tổng hợp
function generatePayrollTable(array $employees, array $timesheet, array $adjustments): array {
    cleanTimesheet($timesheet);
    $workingDays = calculateWorkingDays($timesheet);
    $payroll = [];

    foreach ($employees as $employee) {
        $id = $employee['id'];
        $days = $workingDays[$id] ?? 0;
        $allowance = $adjustments[$id]['allowance'] ?? 0;
        $deduction = $adjustments[$id]['deduction'] ?? 0;
        $netSalary = calculateNetSalary($employee, $days, $adjustments);
        $payroll[] = [
            'id' => $id,
            'name' => $employee['name'],
            'days' => $days,
            'base_salary' => $employee['base_salary'],
            'allowance' => $allowance,
            'deduction' => $deduction,
            'net_salary' => $netSalary
        ];
    }

    return $payroll;
}

// Hàm tính tổng quỹ lương
function getTotalSalary(array $payroll): float {
    return array_sum(array_column($payroll, 'net_salary')); 
}

// Hàm tìm nhân viên có ngày công cao nhất/thấp nhất
function findMaxMinWorkingDays(array $timesheet, array $employees): array {
    $workingDays = calculateWorkingDays($timesheet);
    $daysList = array_values($workingDays);
    sort($daysList);
    $minDays = $daysList[0];
    $maxDays = end($daysList);

    $employeeMap = array_column($employees, 'name', 'id');
    $result = ['max' => [], 'min' => []];

    foreach ($workingDays as $id => $days) {
        if ($days === $maxDays) {
            $result['max'][] = [$employeeMap[$id], $days];
        }
        if ($days === $minDays) {
            $result['min'][] = [$employeeMap[$id], $days];
        }
    }

    return $result;
}

// Hàm lọc nhân viên có ngày công >= 4
function filterEmployeesByWorkingDays(array $timesheet, array $employees, int $threshold = 4): array {
    $workingDays = calculateWorkingDays($timesheet);
    $employeeMap = array_column($employees, 'name', 'id');

    return array_filter($workingDays, function ($days) use ($threshold) {
        return $days >= $threshold;
    }, ARRAY_FILTER_USE_BOTH); 
}

// Hàm kiểm tra dữ liệu
function checkEmployeeData(int $employeeId, string $date, array $timesheet, array $adjustments): array {
    $workedOnDate = in_array($date, $timesheet[$employeeId] ?? []);
    $hasAdjustments = array_key_exists($employeeId, $adjustments); 
    return ['worked' => $workedOnDate, 'has_adjustments' => $hasAdjustments];
}

// Hàm cập nhật dữ liệu
function updateData(array &$employees, array &$timesheet): void {
    $newEmployees = [
        ['id' => 104, 'name' => 'Phạm Thị D', 'base_salary' => 5200000]
    ];
    $employees = array_merge($employees, $newEmployees); 

    // Thêm/xóa ngày công
    if (isset($timesheet[101])) array_push($timesheet[101], '2025-03-07'); 
    if (isset($timesheet[102])) array_unshift($timesheet[102], '2025-03-02'); 
    if (isset($timesheet[103])) array_pop($timesheet[103]); 
    if (isset($timesheet[103]) && count($timesheet[103]) > 0) array_shift($timesheet[103]); 
}

// Thực thi logic
updateData($employees, $timesheet);
$payroll = generatePayrollTable($employees, $timesheet, $adjustments);
$totalSalary = getTotalSalary($payroll);
$maxMin = findMaxMinWorkingDays($timesheet, $employees);
$filteredEmployees = filterEmployeesByWorkingDays($timesheet, $employees);
$checkData = checkEmployeeData(102, '2025-03-03', $timesheet, $adjustments);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Chấm công và Tính lương</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2, h3 {
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
        .summary {
            width: 80%;
            margin: 20px auto;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <h2>HỆ THỐNG CHẤM CÔNG VÀ TÍNH LƯƠNG</h2>

    <h3>Bảng lương tổng hợp tháng 03/2025</h3>
    <table>
        <tr>
            <th>Mã NV</th>
            <th>Họ tên</th>
            <th>Ngày công</th>
            <th>Lương cơ bản</th>
            <th>Phụ cấp</th>
            <th>Khấu trừ</th>
            <th>Lương thực lĩnh</th>
        </tr>
            <?php foreach ($payroll as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['days']; ?></td>
                    <td><?php echo number_format($row['base_salary']); ?></td>
                    <td><?php echo number_format($row['allowance']); ?></td>
                    <td><?php echo number_format($row['deduction']); ?></td>
                    <td><?php echo number_format($row['net_salary']); ?></td>
                </tr>
            <?php endforeach; ?>
    </table>

    <div class="summary">
        <h3>Tổng quỹ lương</h3>
        <p>Tổng quỹ lương tháng 03/2025: <?php echo number_format($totalSalary); ?> VNĐ</p>

        <h3>Nhân viên có ngày công cao nhất và thấp nhất</h3>
        <p>Nhân viên làm nhiều nhất: <?php echo implode(', ', array_map(fn($e) => "{$e[0]} ({$e[1]} ngày công)", $maxMin['max'])); ?></p>
        <p>Nhân viên làm ít nhất: <?php echo implode(', ', array_map(fn($e) => "{$e[0]} ({$e[1]} ngày công)", $maxMin['min'])); ?></p>

        <h3>Danh sách nhân viên có ngày công >= 4</h3>
        <ul>
            <?php
            $employeeMap = array_column($employees, 'name', 'id');
            foreach ($filteredEmployees as $id => $days): ?>
                <li><?php echo htmlspecialchars($employeeMap[$id] ?? 'Không xác định'); ?> (<?php echo $days; ?> ngày công)</li>
            <?php endforeach; ?>
        </ul>

        <h3>Kiểm tra dữ liệu</h3>
        <p>Trần Thị B có đi làm vào ngày 2025-03-03: <?php echo $checkData['worked'] ? 'Có' : 'Không'; ?></p>
        <p>Thông tin phụ cấp của nhân viên 101 tồn tại: <?php echo $checkData['has_adjustments'] ? 'Có' : 'Không'; ?></p>
    </div>
</body>
</html>