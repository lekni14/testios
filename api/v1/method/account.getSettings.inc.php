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

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $result = array("error" => false,
                    "error_code" => ERROR_SUCCESS);

    // Get new messages count\

    $messages_count = 0;

    if (APP_MESSAGES_COUNTERS) {

        $msg = new msg($dbo);
        $msg->setRequestFrom($accountId);

        $messages_count = $msg->getNewMessagesCount();

        unset($msg);
    }

    $result['messagesCount'] = $messages_count;

    $categories = new categories($dbo);
    $categories->setRequestFrom($accountId);

    $result['categories'] = $categories->getList();

    unset($categories);

    echo json_encode($result);
    exit;
}
