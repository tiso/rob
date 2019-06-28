<?php

//-global settings:-------------------------------------------------------------
\error_reporting(E_ALL); //dev
\ini_set('display_errors', 1); //dev
\session_start();
//-global directories:----------------------------------------------------------
\define('ROOT_DIR', __DIR__);
\define('APP_DIR', \dirname(ROOT_DIR));
//-config:----------------------------------------------------------------------
require APP_DIR . '/config/config.php';
//-autoload:--------------------------------------------------------------------
require LIB_DIR . '/autoload.php';
//------------------------------------------------------------------------------
// test
include './spike.php';

//------------------------------------------------------------------------------
use App\App;

App::build();
