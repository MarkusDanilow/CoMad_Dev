<?php

use comad\core\Application;

?>
<!DOCTYPE html>
<html>
<head>
    <title> <?php echo 'CoMad' . $viewData['title']; ?></title>
</head>

<?php
Application::_renderView();
?>

</html>