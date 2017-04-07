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

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;

    $gcm_regId = isset($_POST['gcm_regId']) ? $_POST['gcm_regId'] : '';
    $ios_fcm_regId = isset($_POST['ios_fcm_regId']) ? $_POST['ios_fcm_regId'] : '';

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $clientId = helper::clearInt($clientId);

    $gcm_regId = helper::clearText($gcm_regId);
    $username = helper::clearText($username);
    $password = helper::clearText($password);

    $gcm_regId = helper::escapeText($gcm_regId);
    $username = helper::escapeText($username);
    $password = helper::escapeText($password);

    $ios_fcm_regId = helper::clearText($ios_fcm_regId);
    $ios_fcm_regId = helper::escapeText($ios_fcm_regId);

    if ($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
    }

    $access_data = array();

    $account = new account($dbo);
    $access_data = $account->signin($username, $password);

    unset($account);

    if ($access_data["error"] === false) {

        $account = new account($dbo, $access_data['accountId']);

        switch ($account->getState()) {

            case ACCOUNT_STATE_BLOCKED: {

                break;
            }

            default: {

                $auth = new auth($dbo);
                $access_data = $auth->create($access_data['accountId'], $clientId);

                if ($access_data['error'] === false) {

                    $account->setState(ACCOUNT_STATE_ENABLED);
                    $account->setLastActive();
                    $access_data['account'] = array();

                    array_push($access_data['account'], $account->get());

                    if (strlen($gcm_regId) != 0) {

                        $account->setGCM_regId($gcm_regId);
                    }

                    if (strlen($ios_fcm_regId) != 0) {

                        $account->set_iOS_regId($ios_fcm_regId);
                    }
                }

                break;
            }
        }
    }

    echo json_encode($access_data);
    exit;
}
