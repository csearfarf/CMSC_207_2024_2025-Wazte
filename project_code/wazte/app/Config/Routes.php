<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Generic Routes`
 */
$routes->get('/', 'Login::index');
$routes->get('/login', 'Login::index');
$routes->get('/login/loginWithGoogle', 'Login::loginWithGoogle');
$routes->get('/login/index', 'Login::index');
$routes->get('/login/logout', 'Login::logout');



// Admin Authenticated role route list  
$routes->group('admin', ['filter' => 'roleCheck:1'], function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('index', 'Admin::index');
    $routes->get('users', 'Admin::users');
    $routes->get('facility', 'Admin::facility');
    $routes->get('users/getUsers', 'Users::getUsers'); // list in datatables
    $routes->post('inquiries', 'Facilitator::inquiries');
    $routes->post('users/createNewuser', 'Users::createNewuser'); // create new user endpoint
    $routes->delete('users/remove/(:num)', 'Users::deleteUser/$1'); //deletes user
    $routes->get('users/view/(:num)', 'Users::viewUser/$1'); //view specific user
    $routes->post('users/update', 'Users::updateUser'); // update user endpoint
});

$routes->group('facilitator', ['filter' => 'roleCheck:2'], function ($routes) {
    $routes->get('/', 'Facilitator::index');
    $routes->get('index', 'Facilitator::index');
    $routes->get('facility', 'Facilitator::facility');
    $routes->get('inquiries', 'Facilitator::inquiries');
});


// User Authenticated role route list  
$routes->group('user', ['filter' => 'roleCheck:3'], function ($routes) {
    $routes->get('/', 'User::index');
});

$routes->get('/login/chooseusertype', 'Login::chooseUserType');
$routes->get('/login/chooseType/(:num)', 'Login::chooseType/$1');



// Facilitator Authenticated role route list  
$routes->group('navigate', ['filter' => 'roleCheck:4'], function ($routes) {
    $routes->get('/', 'Navigate::index');
    $routes->get('index', 'Navigate::index');
    $routes->get('materialTypes', 'Navigate::materialTypes');

});

// Facility functio with role 1 & 2 

$routes->group('facility', ['filter' => 'roleCheck:1,2'], function ($routes) {
    // New POST endpoint for saving
    $routes->post('saveNewfacility', 'Facility::createNewFacility');
});





$routes->get('/errors/unauthorized', 'Error::unauthorized');


/**
 * Override eerror page`
 */

$routes->set404Override('App\Controllers\Error::unauthorized');

