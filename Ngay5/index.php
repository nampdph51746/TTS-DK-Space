<?php
require_once 'includes/header.php';
require_once 'includes/logger.php';
require_once 'includes/upload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = htmlspecialchars($_POST['action'] ?? '');
    $file = handleUpload();
    writeLog($action, $file);
    echo "<p class='success'>Đã ghi nhật ký thành công!</p>";
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Mô tả hành động:</label><br>
    <input type="text" name="action" required><br><br>

    <label>File minh chứng (PDF, JPG, PNG):</label><br>
    <input type="file" name="proof"><br><br>

    <button type="submit">Ghi nhật ký</button>
</form>
</body>
</html>
