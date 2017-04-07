<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;

        $itemId = helper::clearInt($itemId);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $item = new items($dbo);
        $item->setRequestFrom(auth::getCurrentUserId());

        $itemInfo = $item->info($itemId);

        if ($itemInfo['error'] === true || $itemInfo['fromUserId'] != auth::getCurrentUserId()) {

            echo json_encode($result);
            exit;

        } else {

            $result = $item->remove($itemId);
        }

        echo json_encode($result);
        exit;
    }
