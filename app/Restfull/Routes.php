<?php
$routes->setAutoRoute(false);
$routes->set404Override(function() {
	return \Appkita\CI4Restfull\ErrorOutput::error404();
});

$routes->setTranslateURIDashes(true);
$listctrl = array_filter(array_diff(scandir(APPPATH.'Controllers/'), array('.', '..'))); 
$issetRoter = [];
foreach ($listctrl as $ctrl) {
    $xt = \strtolower(\pathinfo($ctrl, PATHINFO_EXTENSION));
    if (!empty($ctrl)){
        if ($xt === 'php') {
            $_classname = strtolower(basename($ctrl, ".php"));
            if (!in_array($_classname, $issetRoter)) {
                $routes->resource($_classname);
                array_push($issetRoter, $_classname);
            }
        }
    }
}
$routes->resource('/', ['controller'=>$routes->getDefaultController()]);