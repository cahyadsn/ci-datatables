<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Employees::index');
$routes->get('employees', 'Employees::index');
$routes->post('employees/getData', 'Employees::getData');
$routes->get('employees/getEmployee/(:num)', 'Employees::getEmployee/$1');
