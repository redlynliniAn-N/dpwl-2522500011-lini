<?php
$route = [];
$route['default_controller'] = 'home';
$route['info/(:any)'] = 'home/info/$1';
$route['pramugari/(:num)'] = 'home/pramugari/$1';