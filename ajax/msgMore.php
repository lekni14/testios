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
        $messages_loaded = isset($_POST['messages_loaded']) ? $_POST['messages_loaded'] : 0;

        $user_id = helper::clearInt($user_id);
        $chat_id = helper::clearInt($chat_id);
        $message_id = helper::clearInt($message_id);
        $messages_loaded = helper::clearInt($messages_loaded);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $messages = new msg($dbo);
        $messages->setRequestFrom(auth::getCurrentUserId());

        if ($chat_id == 0) {

            $chat_id = $messages->getChatId(auth::getCurrentUserId(), $user_id);
        }

        if ($chat_id != 0) {

            $result = $messages->getPreviousMessages($chat_id, $message_id);

            ob_start();

            foreach (array_reverse($result['messages']) as $key => $value) {

                draw::messageItem($value, $LANG, $helper);

                $messages_loaded++;
            }

            $result['html'] = ob_get_clean();
            $result['items_all'] = $messages->messagesCountByChat($chat_id);
            $result['items_loaded'] = $messages_loaded;

            if ($messages_loaded < $result['items_all']) {

                ob_start();

                ?>

                    <div class="row more_cont">
                        <div class="col s12">
                            <a href="javascript:void(0)" onclick="Messages.more('<?php echo $chat_id ?>', '<?php echo $user_id ?>'); return false;">
                                <button class="btn waves-effect waves-light <?php echo SITE_THEME; ?> more_link"><?php echo $LANG['action-more']; ?></button>
                            </a>
                        </div>
                    </div>

                <?php

                $result['html2'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }
