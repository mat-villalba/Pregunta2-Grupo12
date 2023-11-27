<?php
include_once('Configuration.php');
include_once ('helpers/SessionManager.php');
include_once ('helpers/RolManager.php');

$sessionManager = new SessionManager();
$configuration = new Configuration();
$rolManager = new RolManager();

$router = $configuration->getRouter();

$module = $_GET['module'] ?? 'home';
$method = $_GET['action'] ?? 'home';

$idRol = $sessionManager->get('idRol');

if($rolManager->getAccessRol($idRol, $module, $method)){
    $router->route($module, $method);
} else {
    $router->route('home', $method);
}