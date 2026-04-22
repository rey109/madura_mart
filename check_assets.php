<?php
$dirsToSearch = ["resources/views", "app/Http/Controllers"];
$assetDirs = ["public/be", "public/images", "public/assets", "resources/css", "resources/js", "resources/images"];

$filesToSearch = [];
foreach ($dirsToSearch as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $filesToSearch[] = $file->getPathname();
        }
    }
}

$assets = [];
foreach ($assetDirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $assets[] = $file->getPathname();
        }
    }
}

$unusedAssets = [];

foreach ($assets as $asset) {
    $assetName = basename($asset);
    
    // Skip some common files
    if (in_array($assetName, ["index.php", ".htaccess", "favicon.ico", "robots.txt", "web.config"])) continue;

    $isUsed = false;
    foreach ($filesToSearch as $file) {
        $content = file_get_contents($file);
        if (strpos($content, $assetName) !== false) {
            $isUsed = true;
            break;
        }
    }

    if (!$isUsed) {
        $unusedAssets[] = $asset;
    }
}

file_put_contents("unused_assets.json", json_encode($unusedAssets, JSON_PRETTY_PRINT));
echo "Found " . count($unusedAssets) . " unused assets. Saved to unused_assets.json\n";
?>
