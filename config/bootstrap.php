<?php
require 'vendor/autoload.php';
require_once 'core/model.php';
require_once 'core/view.php';
require_once 'core/controller.php';
require_once 'core/main.php';
require_once 'config/config.php';

$main = new Main();

$main->createController();
$main->showPage();