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

    $funds = isset($_POST['funds']) ? $_POST['funds'] : 0;

    $funds = helper::clearInt($funds);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $account = new account($dbo, $accountId);
    $result = $account->setBalance($account->getBalance() + $funds);

    if ($result['error'] === false) {

        $result['balance'] = $account->getBalance();
    }

    echo json_encode($result);
    exit;
}
