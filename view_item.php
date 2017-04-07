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
        exit;
    }

    $page_id = "view_item";

    $error = false;
    $error_message = '';

    if (isset($_GET['id'])) {

        $itemId = isset($_GET['id']) ? $_GET['id'] : 0;

        $itemId = helper::clearInt($itemId);

        $item = new items($dbo);
        $item->setRequestFrom(auth::getCurrentUserId());

        $itemInfo = $item->info($itemId);

        if ($itemInfo['error'] === true || $itemInfo['removeAt'] != 0) {

            header("Location: /");
            exit;

        } else {

            $comments = new comments($dbo);
            $comments->setRequestFrom(auth::getCurrentUserId());

            $itemInfo['comments'] = $comments->get($itemId, 0);

            if ($itemInfo['imagesCount'] > 0) {

                $images = new images($dbo);
                $images->setRequestFrom(auth::getCurrentUserId());

                $itemInfo['images'] = $images->get($itemId);

                unset($images);
            }
        }

    } else {

        header("Location: /");
        exit;
    }

    $css_files = array("my.css", "account.css");
    $page_title = "View Item";

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_header.inc.php");

?>

<body>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_topbar.inc.php");
    ?>

<main class="content">
    <div class="container">
        <div class="row">
            <div class="col s12 m12 l12 item_cont">

                <?php

                    draw($itemInfo, $helper, $LANG);

                ?>

                <?php

                    if ($itemInfo['fromUserId'] == auth::getCurrentUserId()) {

                        ?>

                            <div class="fixed-action-btn" style="bottom: 65px; right: 24px;">
                                <a href="/profile/edit_item.php/?id=<?php echo $itemInfo['id']; ?>" class="btn-floating btn-large <?php echo SITE_THEME; ?>">
                                    <i class="material-icons">create</i>
                                </a>
                            </div>

                        <?php

                    } else {

                        if (!$itemInfo['myLike']) {

                            ?>

                                <div class="fixed-action-btn" style="bottom: 65px; right: 24px;">
                                    <a onclick="Items.addToFavorites('<?php echo $itemInfo['id']; ?>'); return false;" class="btn-floating btn-large <?php echo SITE_THEME; ?>">
                                        <i class="material-icons">grade</i>
                                    </a>
                                </div>

                            <?php

                        } else {

                            ?>

                                <div class="fixed-action-btn" style="bottom: 65px; right: 24px;">
                                    <a onclick="Items.removeFromFavorites('<?php echo $itemInfo['id']; ?>'); return false;" class="btn-floating btn-large <?php echo SITE_THEME; ?>">
                                        <i class="material-icons">grade</i>
                                    </a>
                                </div>

                            <?php
                        }
                    }

                ?>

            </div>
        </div>
    </div>
</main>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_footer.inc.php");
    ?>

        <script type="text/javascript">

            window.Comments || ( window.Comments = {} );

            Comments.remove = function (commentId, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/comment/remove.php',
                    data: 'commentId=' + commentId + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('li.collection-item[data-id=' + commentId + ']').remove();

                        if ($('li.collection-item').length == 0) {

                            $("ul.comments-cont").hide();
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Comments.reply = function (commentId, replyToUserId, replyToUserUsername) {

                $("input[name=replyToUserId]").val(replyToUserId);
                $("input[name=comment_text]").val("@" + replyToUserUsername + ", ");
                $("input[name=comment_text]").focus();
            };

            Comments.create = function (itemId, access_token ) {

                var comment_text = $('input[name=comment_text]').val();
                var replyToUserId = $('input[name=replyToUserId]').val();

                $.ajax({
                    type: 'POST',
                    url: '/ajax/comment/new.php',
                    data: 'itemId=' + itemId + "&access_token=" + access_token + "&commentText=" + comment_text + "&replyToUserId=" + replyToUserId,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $("input[name=comment_text]").val("");
                        $("input[name=replyToUserId]").val("0");

                        if (response.hasOwnProperty('html')) {

                            $("ul.comments-cont").append(response.html);
                            $("ul.comments-cont").show();
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            window.Items || ( window.Items = {} );

            Items.addToFavorites = function (itemId) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/item/like.php',
                    data: 'itemId=' + itemId,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.fixed-action-btn').remove();

                        Materialize.toast('<?php echo $LANG['msg-added-to-favorites']; ?>', 2000)

                        if (response.hasOwnProperty('html')){

                            $("div.item_cont").append(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Items.removeFromFavorites = function (itemId) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/item/like.php',
                    data: 'itemId=' + itemId,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.fixed-action-btn').remove();

                        Materialize.toast('<?php echo $LANG['msg-removed-from-favorites']; ?>', 2000)

                        if (response.hasOwnProperty('html')){

                            $("div.item_cont").append(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Items.remove = function (itemId, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/item/remove.php',
                    data: 'itemId=' + itemId + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        Materialize.toast('<?php echo $LANG['msg-item-removed']; ?>', 2000)

                        window.location.href = "/stream.php";
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Items.report = function (itemId, abuseId, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/item/report.php',
                    data: 'itemId=' + itemId + "&abuseId=" + abuseId + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        Materialize.toast('<?php echo $LANG['msg-item-reported']; ?>', 2000)

                        $('#report-box').closeModal();
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

</body>
</html>

<?php

    function draw($item, $helper, $LANG)
    {

            $item['itemContent'] = helper::processText($item['itemContent']);

        ?>

            <div class="row item" data-id="<?php echo $item['id']; ?>">
                <div class="col s12">
                    <div class="card">
                        <div class="card-image">
                            <img src="<?php echo $item['imgUrl']; ?>">
                            <span class="card-title"><?php echo $item['categoryTitle']; ?></span>
                        </div>
                        <div class="card-content">

                            <?php

                                if (array_key_exists("images", $item)) {

                                    $images = $item['images'];

                                    if (count($images['items']) > 0) {

                                        ?>
                                            <div class="row">
                                                <div class="col s12 m12 l12 left" style="padding: 0;">
                                        <?php

                                        $img_items = $images['items'];

                                        for ($i = 0; $i < count($images['items']); $i++) {


                                            $img = $img_items[$i];

                                            ?>

                                                    <div class="col s12 m4 item" style="">
                                                        <div class="card">
                                                            <div class="card-image">
                                                                <img class="item-img" src="<?php echo $img['imgUrl']; ?>" style="height: auto">
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                        }

                                        ?>
                                                </div>
                                            </div>
                                        <?php
                                    }
                                }
                            ?>

                            <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 10px; font-weight: bold"><?php echo $item['price']; ?><?php echo $LANG['label-currency']; ?></p>
                            <p style="margin-bottom: 10px; font-weight: bold"><h5><?php echo $item['itemTitle']; ?></h5></p>
                            <p><?php echo $item['itemContent']; ?></p>
                            <ul class="collection" style="border-bottom: none;">
                                <li class="collection-item avatar" style="min-height: inherit">
                                    <a href="/profile.php?id=<?php echo $item['fromUserId']; ?>"><img src="<?php if ( strlen($item['fromUserPhoto']) != 0 ) { echo $item['fromUserPhoto']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="" class="circle"></a>
                                    <span class="title dialogs-title"><?php echo $item['fromUserFullname']; ?></span>

                                    <?php

                                        if (strlen($item['fromUserPhone'])) {

                                            ?>
                                                <p>
                                                    <?php echo $item['fromUserPhone']; ?>
                                                    <br>
                                                </p>
                                            <?php
                                        }
                                    ?>
                                    <p><?php echo $item['timeAgo']; ?></p>
                                </li>
                            </ul>
                        </div>
                        <div class="card-action">
                            <?php

                                if ($item['fromUserId'] == auth::getCurrentUserId()) {

                                    ?>
                                        <a onclick="Items.remove('<?php echo $item['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" href="javascript:void(0)"><?php echo $LANG['action-remove']; ?></a>
                                    <?php

                                } else {

                                    ?>
                                        <a class="modal-trigger" href="#report-box"><?php echo $LANG['action-report']; ?></a>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                if ($item['allowComments'] == 1) {




                $comments = $item['comments'];
            ?>

            <div class="row item" data-id="<?php echo $item['id']; ?>">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">

                            <span class="card-title"><?php echo $LANG['label-placeholder-comments']; ?></span>

                            <ul class="collection comments-cont" style="border-bottom: none; <?php if (count($comments['comments']) == 0) echo "display: none" ?>">

                            <?php

                                if (count($comments['comments']) > 0) {

                                    $comments['comments'] = array_reverse($comments['comments'], false);

                                    foreach ($comments['comments'] as $key => $value) {

                                        draw::commentItem($value, $LANG, $helper);

                                    }
                                }
                            ?>

                            </ul>

                        </div>
                        <div class="card-action">
                            <div class="row msg-form">
                                <form class="" onsubmit="Comments.create('<?php echo $item['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;">
                                    <input type="hidden" name="replyToUserId" value="0">

                                    <div class="input-field col s9">
                                        <input type="text" class="validate" id="msg-text" name="comment_text" value="">
                                        <label for="msg-text" class=""><?php echo $LANG['label-placeholder-comment']; ?></label>
                                    </div>

                                    <div class="input-field col s1">
                                        <button type="submit" class="btn waves-effect waves-light <?php echo SITE_THEME; ?> btn-large" name=""><i class="material-icons">send</i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                }
            ?>


            <div id="report-box" class="modal">
                <div class="modal-content">
                    <h4><?php echo $LANG['action-report']; ?></h4>
                    <a onclick="Items.report('<?php echo $item['id']; ?>', '0', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-ripple btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-1']; ?></a>
                    <a onclick="Items.report('<?php echo $item['id']; ?>', '1', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-ripple btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-2']; ?></a>
                    <a onclick="Items.report('<?php echo $item['id']; ?>', '2', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-ripple btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-3']; ?></a>
                    <a onclick="Items.report('<?php echo $item['id']; ?>', '3', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-ripple btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-4']; ?></a>
                </div>
                <div class="modal-footer">
                    <a href="#!" class=" modal-action modal-close waves-effect waves-ripple btn-flat"><?php echo $LANG['action-close']; ?></a>
                </div>
            </div>

        <?php
    }
