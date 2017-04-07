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

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $commentText = isset($_POST['commentText']) ? $_POST['commentText'] : '';

        $replyToUserId = isset($_POST['replyToUserId']) ? $_POST['replyToUserId'] : 0;

        $itemId = helper::clearInt($itemId);

        $commentText = helper::clearText($commentText);

        $commentText = preg_replace( "/[\r\n]+/", " ", $commentText); //replace all new lines to one new line
        $commentText  = preg_replace('/\s+/', ' ', $commentText);        //replace all white spaces to one space

        $commentText = helper::escapeText($commentText);

        $replyToUserId = helper::clearInt($replyToUserId);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (strlen($commentText) != 0) {

            $items = new items($dbo);
            $items->setRequestFrom(auth::getCurrentUserId());

            $itemInfo = $items->info($itemId);

            if ($itemInfo['allowComments'] == 0) {

                exit;
            }

            $comments = new comments($dbo);
            $comments->setRequestFrom(auth::getCurrentUserId());

            $notifyId = 0;

            $result = $comments->create($itemId, $commentText, $notifyId, $replyToUserId);

            ob_start();

            draw::commentItem($result['comment'], $LANG, $helper);

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }
