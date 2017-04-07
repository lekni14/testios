<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

    session_start();

    error_reporting(E_ALL);

    include_once($_SERVER['DOCUMENT_ROOT']."/config/db.inc.php");
    include_once($_SERVER['DOCUMENT_ROOT']."/config/lang.inc.php");

    foreach ($C as $name => $val) {

        define($name, $val);
    }

    foreach ($B as $name => $val) {

        define($name, $val);
    }

    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;

    $dbo = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    function __autoload($class)
    {
        $filename = $_SERVER['DOCUMENT_ROOT']."/class/class.".$class.".inc.php";

        if (file_exists($filename)) {

            include_once($filename);
        }
    }

    ini_set('session.cookie_domain', '.'.APP_HOST);
    session_set_cookie_params(0, '/', '.'.APP_HOST);

    $helper = new helper($dbo);
    $auth = new auth($dbo);

    if (!auth::isSession() && isset($_COOKIE['user_name']) && isset($_COOKIE['user_password'])) {

        $account = new account($dbo, $helper->getUserId($_COOKIE['user_name']));

        $accountInfo = $account->get();

        if ($accountInfo['error'] === false && $accountInfo['state'] == ACCOUNT_STATE_ENABLED) {

            $auth = new auth($dbo);

            if ($auth->authorize($accountInfo['id'], $_COOKIE['user_password'])) {

                auth::setSession($accountInfo['id'], $accountInfo['username'], $accountInfo['fullname'], $accountInfo['lowPhotoUrl'], $accountInfo['verify'], $account->getAccessLevel($accountInfo['id']), $_COOKIE['user_password']);

                $account->setLastActive();

            } else {

                auth::clearCookie();
            }

        } else {

            auth::clearCookie();
        }
    }
