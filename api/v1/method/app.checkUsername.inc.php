<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/config/api.inc.php");

if (!empty($_POST)) {

    $username = isset($_POST['username']) ? $_POST['username'] : '';

    $username = helper::clearText($username);

    $result = array("error" => true);

    if (!$helper->isLoginExists($username)) {

        $result = array("error" => false);
    }

    echo json_encode($result);
    exit;
}
