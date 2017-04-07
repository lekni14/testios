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

    $chat_id = 0;
    $user_id = 0;

    if (!empty($_POST)) {

        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        $chat_id = isset($_POST['chat_id']) ? $_POST['chat_id'] : 0;
        $message_id = isset($_POST['message_id']) ? $_POST['message_id'] : 0;

        $message_text = isset($_POST['message_text']) ? $_POST['message_text'] : "";
        $message_img = isset($_POST['message_img']) ? $_POST['message_img'] : "";

        $user_id = helper::clearInt($user_id);
        $chat_id = helper::clearInt($chat_id);
        $message_id = helper::clearInt($message_id);

        $message_text = helper::clearText($message_text);

        $message_text = preg_replace( "/[\r\n]+/", "<br>", $message_text); //replace all new lines to one new line
        $message_text  = preg_replace('/\s+/', ' ', $message_text);        //replace all white spaces to one space

        $message_text = helper::escapeText($message_text);

        $message_img = helper::clearText($message_img);
        $message_img = helper::escapeText($message_img);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if ($access_token != auth::getAccessToken()) {

            api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        }

        $profile = new profile($dbo, $user_id);
        $profile->setRequestFrom(auth::getCurrentUserId());

        $profileInfo = $profile->getShort();

        if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

            echo json_encode($result);
            exit;
        }

        if ($profileInfo['allowMessages'] == 0) {

            echo json_encode($result);
            exit;
        }

        if (!$profileInfo['inBlackList']) {

            $messages = new msg($dbo);
            $messages->setRequestFrom(auth::getCurrentUserId());

            $result = $messages->create($user_id, $chat_id, $message_text, $message_img, 0, 0, $profileInfo['gcm_regid'], 0, $profileInfo['ios_fcm_regid']);

            $chat_id = $result['chatId'];

            $result = $messages->getNextMessages($result['chatId'], $message_id);

            ob_start();

            foreach ($result['messages'] as $key => $value) {

                draw::messageItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();
            $result['items_all'] = $messages->messagesCountByChat($chat_id);
            $result['chat_id'] = $chat_id;
        }

        echo json_encode($result);
        exit;
    }
