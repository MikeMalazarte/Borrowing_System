<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'BorrowSys_Ctrl::index');  // ← fixes the 404 on localhost


////////////////////ROUTES FOR BORROWING SYSTEM////////////////
$routes->get('Borrowing-System', 'BorrowSys_Ctrl::index');
$routes->post('Borrowing-System', 'BorrowSys_Ctrl::index');