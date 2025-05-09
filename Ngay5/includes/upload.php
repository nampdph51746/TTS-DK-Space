<?php
function handleUpload() {
    if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
        return null; // Không có file hoặc lỗi upload
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    $fileType = $_FILES['proof']['type'];
    $fileSize = $_FILES['proof']['size'];
    $tmpName = $_FILES['proof']['tmp_name'];
    $ext = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);

    if (!in_array($fileType, $allowedTypes) || $fileSize > $maxSize) {
        return null;
    }

    $newName = 'upload_' . time() . '.' . $ext;
    $destination = 'uploads/' . $newName;

    if (move_uploaded_file($tmpName, $destination)) {
        return $newName;
    }

    return null;
}
?>
