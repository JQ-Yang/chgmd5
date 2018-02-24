<?php
/**
 * 更改文件的MD5值
 *
 * @param string      $file     原文件路径
 * @param string|null $new_file 新文件路径，为null表示覆盖原文件
 *
 * @return string
 */
function chgFileMd5($file = '', $new_file = null)
{
    if (!is_file($file)) {
        echo '==============路径：' . $file . ' 文件不存在' . PHP_EOL;
        return false;
    }
    $md5 = md5_file($file);
    if (!$md5) {
        echo '==============路径：' . $file . ' 获取文件MD5信息失败' . PHP_EOL;
        return false;
    }
    /*$content = file_get_contents($file);
    if ($content === false) {
        echo '==============路径：' . $file . ' 获取文件MD5信息失败' . PHP_EOL;
        return false;
    }*/
    if (is_null($new_file)) {
        $new_file = $file;
    }
    $handle = fopen($new_file, 'a');
    fwrite($handle, ' ');
    fclose($handle);
    /*$new_content = file_put_contents($new_file, $content . ' ');
    if ($new_content === false) {
        echo '==============路径：' . $file . ' 更改文件MD5信息失败' . PHP_EOL;
        return false;
    }*/
    echo '文件：' . $file . ' 更改md5成功，原md5：' . $md5 . '，新md5：' . md5_file($new_file) . PHP_EOL;
    return $file;
}


/**
 * 复制文件并更改md5值
 *
 * @param string $old_file_path 旧文件夹路径
 * @param null   $new_file_path 新文件夹路径，为NULL表示覆盖
 *
 * @return bool
 */
function copyFileAndChgMd5($old_file_path = '', $new_file_path = null)
{
    $dh = opendir($old_file_path);
    if (!$dh) {
        echo '打开目录失败：' . $old_file_path . PHP_EOL;
        return false;
    }
    while (($file = readdir($dh)) !== false) {
        if (in_array($file, ['.', '..'])) {
            continue;
        }
        if (is_dir($old_file_path . $file)) {
            copyFileAndChgMd5($old_file_path . $file . DIRECTORY_SEPARATOR,
                $new_file_path . $file . DIRECTORY_SEPARATOR);
        } else {
            if (is_null($new_file_path)) {
                $new_file_path = $old_file_path;
            }
            if ($old_file_path != $new_file_path) {
                if (!is_dir($new_file_path) && mkdir($new_file_path) === false) {
                    echo '创建新目录失败：' . $new_file_path . PHP_EOL;
                }
                if (copy($old_file_path . $file, $new_file_path . $file) === false) {
                    echo '复制文件失败：' . $new_file_path . PHP_EOL;
                }
            }
            chgFileMd5($old_file_path . $file, $new_file_path . $file);
            //var_dump($new_file_path . $file);
        }
    }
    closedir($dh);
}

ini_set('memory_limit', -1);
$time          = microtime(true);
$dir_path      = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$old_file_path = $dir_path . 'old_file' . DIRECTORY_SEPARATOR;
$new_file_path = $dir_path . 'new_file' . DIRECTORY_SEPARATOR;

copyFileAndChgMd5($old_file_path, $new_file_path);

printf('脚本运行时间为%.2f', microtime(true) - $time);
echo PHP_EOL;
echo round(memory_get_usage(true) / 1024, 2) . " KB";
echo PHP_EOL;