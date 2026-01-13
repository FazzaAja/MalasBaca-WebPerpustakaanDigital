<?php
// layout/header.php
// Ensure pages can set $path ('' for root, '../' for subfolders). Default to empty string when not provided.
if (!isset($path)) $path = '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MalasBaca</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    
    <?php
    // Prefer a single global stylesheet at project root (e.g., /responsi/style.css)
    // Compute an app-root relative URL by subtracting DOCUMENT_ROOT from project real path.
    $projectPath = realpath(__DIR__ . '/..');
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT']);
    $appBaseUrl = str_replace('\\', '/', str_replace($docRoot, '', $projectPath));
    if ($appBaseUrl === '') $appBaseUrl = '/';
    // Ensure it starts with a slash
    if ($appBaseUrl[0] !== '/') $appBaseUrl = '/' . $appBaseUrl;
    // Trim trailing slash to avoid double slashes
    $appBaseUrl = rtrim($appBaseUrl, '/');

    // Final CSS href points to single style.css in project root
    $cssHref = $appBaseUrl . '/style.css';
    // Add cache-busting based on file modification time to avoid stale CSS in browser
    $cssFilePath = $projectPath . '/style.css';
    if (file_exists($cssFilePath)) {
        $cssHref .= '?v=' . filemtime($cssFilePath);
    }
    ?>
    <!-- CSS loaded: <?php echo $cssHref; ?> -->
    <link rel="stylesheet" href="<?php echo $cssHref; ?>" />
</head>
<body>
    <div class="container">