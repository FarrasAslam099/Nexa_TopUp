<?php
// includes/header.php — HTML Head

$pageTitle       = $pageTitle       ?? 'Nexa_Topup — Top Up Mobile Legends';
$pageDescription = $pageDescription ?? 'Platform top up Mobile Legends terpercaya. Diamond, Membership, Bundle, Weekly Pass — harga termurah, proses instan 24/7.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= sanitize($pageDescription) ?>">
  <title><?= sanitize($pageTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/style.css">
</head>
<body>
