<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

include_once($_SERVER['DOCUMENT_ROOT'] . "/core/init.inc.php");

if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

    header('Location: /');
}

if (!empty($_POST)) {

    $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

    $commentId = isset($_POST['commentId']) ? $_POST['commentId'] : 0;

    $commentId = helper::clearInt($commentId);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $comments = new comments();
    $comments->setRequestFrom(auth::getCurrentUserId());

    $commentInfo = $comments->info($commentId);

    if ($commentInfo['fromUserId'] == auth::getCurrentUserId()) {

        $comments->remove($commentId);

    } else {

        $items = new items($dbo);
        $items->setRequestFrom(auth::getCurrentUserId());

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
