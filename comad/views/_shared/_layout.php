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

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/icon.png"/>

</head>

<?php
Application::_renderView();
?>

</html>