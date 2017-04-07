<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */s

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    if (!empty($_POST)) {

        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : 0;

        $reason = isset($_POST['reason']) ? $_POST['reason'] : 0;

        $profile_id = helper::clearInt($profile_id);

        $reason = helper::clearInt($reason);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $profile = new profile($dbo, $profile_id);
        $profile->setRequestFrom(auth::getCurrentUserId());

        if ($reason >= 0 && $reason < 4) {

            $result = $profile->reportAbuse($reason);
        }

        echo json_encode($result);
        exit;
    }
