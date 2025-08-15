<?php
$page = $_GET['page'] ?? 'home';
switch ($page) {
  case 'dashboard':
      require_once('dashboard.php');
    break;
  default:
      require_once('homePage.php');
    break;
}