<?php
// includes/header.php
// Opens <html>, <head>. Include session.php and functions.php BEFORE this file
// in every page, since this file uses isLoggedIn() / $pdo.
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - Phone Shop' : 'Phone Shop'; ?></title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/responsive.css" />
</head>
    <script src="./js/script.js"></script>
<body>