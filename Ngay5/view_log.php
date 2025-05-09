<?php require_once 'includes/header.php'; ?>

<form method="GET">
    <label>Chọn ngày:</label>
    <input type="date" name="log_date" required>
    <button type="submit">Xem log</button>
</form>

<?php
if (isset($_GET['log_date'])) {
    $date = $_GET['log_date'];
    $logFile = "logs/log_$date.txt";

    if (file_exists($logFile)) {
        echo "<ul class='log-entries'>";
        $fp = fopen($logFile, 'r');
        while (!feof($fp)) {
            $line = fgets($fp);
            if (trim($line) === '') continue;

            $color = '';
            if (str_contains($line, 'thất bại')) {
                $color = 'style=\"color:red\"';
            }

            echo "<li $color>" . htmlspecialchars($line) . "</li>";

            // Kiểm tra xem dòng log có chứa file là ảnh không
            if (preg_match('/File: ([\w\-_\.]+\.(jpg|png))/i', $line, $matches)) {
                $imgPath = 'uploads/' . $matches[1];
                if (file_exists($imgPath)) {
                    echo "<div style='margin-bottom: 20px;'><img src='$imgPath' style='max-width:300px; border:1px solid #ccc; border-radius:8px;'></div>";
                }
            }
        }
        fclose($fp);
        echo "</ul>";
    } else {
        echo "<p class='error'>Không có nhật ký cho ngày này.</p>";
    }
}
?>
</body>
</html>
