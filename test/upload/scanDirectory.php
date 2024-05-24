<?php
// 确保 $base_dir 被定义并且给一个默认值
$base_dir = __DIR__ . "/file/";
//if (!isset($base_dir) || $base_dir === null) {
//    exit("Error: Base directory path is not defined.");
//}

// 获取传入的路径参数，并确保它是相对于 $base_dir 的安全路径
$path = isset($_GET['path']) ? $_GET['path'] : '';
$dir = realpath($base_dir . DIRECTORY_SEPARATOR . $path);

//if ($dir === false || strpos($dir, $base_dir) !== 0) {
//    // 如果 $dir 无效或不在 $base_dir 中，则退出
//    exit("Error: Invalid directory path.");
//}

function scanDirDir($dir) {
    global $base_dir; // 引入全局变量 $base_dir
    $scan = scandir($dir);
    foreach ($scan as $index => $item) {
        if ($item === '.' || $item === '..') continue;

        $path = realpath($dir . DIRECTORY_SEPARATOR . $item);
        if (is_dir($path)) {
            yield [
                'name' => $item,
                'path' => str_replace($base_dir, '', $path ?? ''), // 确保 $path 不为 null
                'type' => 'directory'
            ];
        } else {
            yield [
                'name' => $item,
                'path' => str_replace($base_dir, '', $path ?? ''),
                'type' => 'file'
            ];
        }
    }
}

// 使用生成器收集所有数据
$structureGenerator = scanDirDir($dir);
// 转换生成器为数组
$structure = iterator_to_array($structureGenerator, false);

header('Content-Type: application/json');
echo json_encode($structure);
?>
