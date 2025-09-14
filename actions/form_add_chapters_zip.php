<?php
require_once __DIR__ . '/../includes/core.php';

if (!$auth->isLoggedIn()) {
    http_response_code(403);
    include __DIR__ . '/../error403.php';
    exit;
}

$idScan = $_POST['id_scan'] ?? null;
if (!$idScan || !isset($_FILES['chapters_zip'])) {
    die('Missing scan ID or ZIP file.');
}

$zipFile = $_FILES['chapters_zip']['tmp_name'];
$zip = new ZipArchive();
if ($zip->open($zipFile) !== true) {
    die('Failed to open ZIP file.');
}

$tempDir = __DIR__ . '/../uploads/tmp_zip_' . uniqid() . '/';
mkdir($tempDir, 0777, true);
$zip->extractTo($tempDir);
$zip->close();

$chapters = array_filter(glob($tempDir . '*'), 'is_dir');

foreach ($chapters as $chapterDir) {
    $folderName = basename($chapterDir);

    if (preg_match('/Ch\.?\s*([0-9]+(?:\.[0-9]+)*)/i', $folderName, $matches)) {
        $parts = explode('.', $matches[1]);
        $parts[0] = ltrim($parts[0], '0');
        if ($parts[0] === '') $parts[0] = '0';
        $chapterNumber = implode('.', $parts);
    } else {
        $chapterNumber = '0';
    }

    $chapterName = $folderName;

    $stmt = $pdo->prepare('INSERT INTO chapters 
        (number, name, id_scan, description, view, like, dislike, datetime, everyImagesLink)
        VALUES (:number, :name, :id_scan, "", 0, 0, 0, :datetime, "")');
    $stmt->execute([
        'number'   => $chapterNumber,
        'name'     => $chapterName,
        'id_scan'  => $idScan,
        'datetime' => time()
    ]);
    $chapterId = $pdo->lastInsertId();

    $uploadDir = __DIR__ . "/../uploads/chapters/$idScan/$chapterId/";
    mkdir($uploadDir, 0777, true);

    $storedNames = [];
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($chapterDir));

    foreach ($rii as $file) {
        if ($file->isDir()) continue;

        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;

        $fileName = basename($file->getFilename());
        copy($file->getPathname(), $uploadDir . $fileName);
        $storedNames[] = $fileName;
    }
    natsort($storedNames);


    foreach ($files as $filePath) {
        $fileName = basename($filePath);
        copy($filePath, $uploadDir . $fileName);
        $storedNames[] = $fileName;
    }

    $everyImagesLink = implode('#~>', $storedNames);
    $stmt = $pdo->prepare('UPDATE chapters SET everyImagesLink = :links WHERE id = :id');
    $stmt->execute([
        'links' => $everyImagesLink,
        'id'    => $chapterId
    ]);
}


function deleteDirRecursive(string $dir): bool
{
    $dir = rtrim($dir, "/\\");
    if (!is_dir($dir)) return true;

    $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

    $failed = [];

    foreach ($files as $file) {
        $path = $file->getRealPath();
        if ($file->isDir()) {
            if (!@rmdir($path)) {
                @chmod($path, 0777);
                if (!@rmdir($path)) $failed[] = $path;
            }
        } else {
            if (!@unlink($path)) {
                @chmod($path, 0666);
                if (!@unlink($path)) $failed[] = $path;
            }
        }
    }

    if (!@rmdir($dir)) {
        @chmod($dir, 0777);
        if (!@rmdir($dir)) $failed[] = $dir;
    }

    if (!empty($failed)) {
        error_log('deleteDir Recursive failed to remove: ' . implode(', ', $failed));
        return false;
    }
    return true;
}


if (!deleteDirRecursive($tempDir)) {
    error_log("Impossible to delete $tempDir. please check permissions");
}


if (!empty($zipFile) && file_exists($zipFile)) {
    if (!@unlink($zipFile)) {
        @chmod($zipFile, 0666);
        if (!@unlink($zipFile)) {
            error_log("Impossible to delete zip file : $zipFile");
        }
    }
}

header("Location: /scan/$idScan?ntf=zip_chapters_added");
exit;
