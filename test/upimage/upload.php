<?php
header('Content-Type: application/json');

$base_dir = __DIR__ . "/file/";

if (!file_exists($base_dir)) {
    mkdir($base_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $way = $_POST['way'] ?? '';

    if ($token === '' && $way === '46231') {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['file'];
            $fileName = uniqid() . '.jpg';
            $filePath = $base_dir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully', 'path' => $filePath]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid token or way parameter']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
