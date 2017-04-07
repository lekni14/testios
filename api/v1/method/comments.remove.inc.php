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

    $commentId = isset($_POST['commentId']) ? $_POST['commentId'] : 0;

    $accountId = helper::clearInt($accountId);

    $commentId = helper::clearInt($commentId);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $comments = new comments();
    $comments->setRequestFrom($accountId);

    $commentInfo = $comments->info($commentId);

    if ($commentInfo['fromUserId'] == $accountId) {

        $comments->remove($commentId);

    } else {

        $items = new items($dbo);
        $items->setRequestFrom($accountId);

        $itemInfo = $items->info($commentInfo['itemId']);

        if ($itemInfo['fromUserId'] != 0 && $itemInfo['fromUserId'] == $accountId) {

            $comments->remove($commentId);
        }
    }

    unset($comments);
    unset($items);

    echo json_encode($result);
    exit;
}
