<?php
$baseDir = __DIR__ . '/file';
$file = isset($_GET['file']) ? $_GET['file'] : '';
$fullPath = realpath($baseDir . '/' . $file);

if (strpos($fullPath, $baseDir) !== 0 || !file_exists($fullPath)) {
    http_response_code(404);
    $message = "文件未找到！";
} else {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fullPath));

    readfile($fullPath);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>文件下载</title>
</head>
<body>
<?php if (isset($message)): ?>
    <div><?php echo $message; ?></div>
<?php endif; ?>
</body>
</html>
