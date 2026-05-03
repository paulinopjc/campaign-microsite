<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Auth
$routes->get('login', 'Auth\LoginController::index');
$routes->post('auth/google', 'Auth\LoginController::google');
$routes->get('logout', 'Auth\LoginController::logout');

// Public microsites
$routes->get('c/(:segment)', 'Public\MicrositeController::show/$1');
$routes->post('c/(:segment)/submit', 'Public\MicrositeController::submit/$1');
$routes->get('c/(:segment)/thanks', 'Public\MicrositeController::thanks/$1');

// Admin
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('campaigns', 'Admin\CampaignController::index');
    $routes->get('campaigns/create', 'Admin\CampaignController::create');
    $routes->post('campaigns/create', 'Admin\CampaignController::store');
    $routes->get('campaigns/(:num)/edit', 'Admin\CampaignController::edit/$1');
    $routes->post('campaigns/(:num)/edit', 'Admin\CampaignController::update/$1');
    $routes->get('campaigns/(:num)/preview', 'Admin\CampaignController::preview/$1');
    $routes->post('campaigns/(:num)/publish', 'Admin\CampaignController::publish/$1');
    $routes->post('campaigns/(:num)/close', 'Admin\CampaignController::close/$1');
    $routes->get('campaigns/(:num)/submissions', 'Admin\CampaignController::submissions/$1');
    $routes->get('campaigns/(:num)/export', 'Admin\CampaignController::export/$1');
});