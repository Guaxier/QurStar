<?php
$base_dir = __DIR__ . "/file/"; // 基本目录，确保此目录存在且可写
$response = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES["fileToUpload"])) {
        $file = $_FILES["fileToUpload"];
        $fileName = basename($file["name"]);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // 检查文件扩展名并设置目标目录
        if (in_array($fileType, ['png', 'jpg', 'jpeg', 'gif', 'mp3', 'mp4', 'pdf', 'doc', 'docx', 'xlsx'])) {
            $target_dir = $base_dir . $fileType . "/";
        } else {
            $target_dir = $base_dir . "other/";
        }

        // 确保目标目录存在
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $fileName;

        // 如果文件已存在，重命名文件
        if (file_exists($target_file)) {
            $fileBaseName = pathinfo($fileName, PATHINFO_FILENAME);
            $i = 1;
            while (file_exists($target_file)) {
                $newFileName = $fileBaseName . $i . '.' . $fileType;
                $target_file = $target_dir . $newFileName;
                $i++;
            }
            $response = "由于文件 " . htmlspecialchars($fileName) . " 已存在，文件已重命名为 " . htmlspecialchars($newFileName) . " 并上传到 " . "/" . $fileType;
        } else {
            $response = "文件 " . htmlspecialchars($fileName) . " 已成功上传到 " . "/" . $fileType;
        }

        // 上传文件
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            // 上传成功，已经在上面设置了$response
        } else {
            $response = "文件上传失败。";
        }
    } else {
        $response = "未检测到上传的文件。";
    }
} else {
    $response = "非POST请求。";
}

echo $response; // 返回给前端的信息
?>
