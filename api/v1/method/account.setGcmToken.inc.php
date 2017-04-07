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

    $gcm_regId = isset($_POST['gcm_regId']) ? $_POST['gcm_regId'] : '';
    $ios_fcm_regId = isset($_POST['ios_fcm_regId']) ? $_POST['ios_fcm_regId'] : '';

    $gcm_regId = helper::clearText($gcm_regId);
    $gcm_regId = helper::escapeText($gcm_regId);

    $ios_fcm_regId = helper::clearText($ios_fcm_regId);
    $ios_fcm_regId = helper::escapeText($ios_fcm_regId);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    if (strlen($gcm_regId) == 0) {

        echo json_encode($result);
        exit;
    }

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        $gcm = new gcm($dbo);

        $id = $gcm->searchGCM_regId($gcm_regId);

        if ($id == 0) {

            $result = $gcm->addGCM_regId($gcm_regId);
        }

        unset($gcm);

    } else {

        $account = new account($dbo, $accountId);

        if (strlen($ios_fcm_regId) != 0) {

            $account->set_iOS_regId($ios_fcm_regId);
        }

        $result = $account->setGCM_regId($gcm_regId);
    }

    echo json_encode($result);
    exit;
}
