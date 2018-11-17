<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

use comad\core\Application;
use comad\core\services\ViewDataService;

?>
<!DOCTYPE html>
<html>
<head>
    <title>CoMad - <?php print ViewDataService::_get(ViewDataService::TITLE); ?></title>

    <!-- load bootstrap -->
    <link rel="stylesheet" type="text/css" href="assets/lib/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/bootstrap/css/bootstrap-grid.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/bootstrap/css/bootstrap-reboot.min.css"/>
    <script type="text/javascript" src="assets/lib/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/lib/bootstrap/js/bootstrap.bundle.js"></script>

    <!-- load fontawesome -->
    <link rel="stylesheet" type="text/css" href="assets/lib/fontawesome/css/all.min.css"/>

    <!-- load jquery -->
    <link rel="stylesheet" type="text/css" href="assets/lib/jquery/css/jquery-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/jquery/css/jquery-ui.structure.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/jquery/css/jquery-ui.theme.min.css"/>
    <script type="text/javascript" src="assets/lib/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="assets/lib/jquery/js/jquery-ui.min.js"></script>

    <!-- load custom stuff -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"/>

    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/icon.png"/>

</head>

<body>


<?php
Application::_renderSection('navbar');
Application::_renderView();
?>


</body>

</html>