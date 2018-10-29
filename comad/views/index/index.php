<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

use comad\core\services\ViewDataService;

$model = ViewDataService::_get(ViewDataService::VIEW_MODEL);

?>

<h1>Homepage</h1>

<?php

print $model->name;

?>
