<?php

/**
 * Bootstrap defines
 */

// This points to the bootstrap CDN by default. You can point it to a local copy if you want.
//define('QCUBED_BOOTSTRAP_CSS', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
//define('QCUBED_BOOTSTRAP_JS', "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");
define('QCUBED_BOOTSTRAP_ASSETS_URL', dirname(QCUBED_BASE_URL) . '/qcubed-4/plugin-bootstrap/assets');

define('QCUBED_BOOTSTRAP_CSS', QCUBED_URL_PREFIX . '/project/assets/bootstrap/css/bootstrap.min.css');
define('QCUBED_BOOTSTRAP_JS', QCUBED_URL_PREFIX . '/project/assets/bootstrap/js/bootstrap.min.js');

//define('QCUBED_BOOTSTRAP_CSS', QCUBED_VENDOR_URL . '/twbs/bootstrap/dist/css/bootstrap.min.css');
//define('QCUBED_BOOTSTRAP_JS', QCUBED_VENDOR_URL . '/twbs/bootstrap/dist/js/bootstrap.min.js');
