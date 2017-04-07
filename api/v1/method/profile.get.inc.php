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

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $profileId = isset($_POST['profileId']) ? $_POST['profileId'] : 0;

    $profileId = helper::clearInt($profileId);

    $accountId = helper::clearInt($accountId);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);


    $profile = new profile($dbo, $profileId);
    $profile->setRequestFrom($accountId);

    if ($accountId != 0) {

        $account = new account($dbo, $accountId);
        $account->setLastActive();
    }

    $result = $profile->get();

    echo json_encode($result);
    exit;
}
