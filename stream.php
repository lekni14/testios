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

    $profile = new account($dbo, auth::getCurrentUserId());

    $profile->setLastActive();

    $stream = new stream($dbo);
    $stream->setRequestFrom(auth::getCurrentUserId());

    $items_all = $stream->getAllCount();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $stream->get($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <div class="row more_cont">
                    <div class="col s12">
                        <a href="javascript:void(0)" onclick="Stream.moreItems('<?php echo $result['itemId']; ?>'); return false;">
                            <button class="btn waves-effect waves-light <?php echo SITE_THEME; ?> more_link"><?php echo $LANG['action-more']; ?></button>
                        </a>
                    </div>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "stream";

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['topbar-stream']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");

?>

<body>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>

<main class="content">

    <div class="container">
        <div class="row">
            <div class="col s12 m12 l12 left stream_cont">

                <h2 class="header"><?php echo $LANG['topbar-stream']; ?></h2>

                                <?php

                                    $result = $stream->get(0);

                                    $items_loaded = count($result['items']);

                                    if ($items_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            draw($value, $LANG, $helper);
                                        }

                                        if ($items_all > 20) {

                                            ?>

                                            <div class="row more_cont">
                                                <div class="col s12">
                                                    <a href="javascript:void(0)" onclick="Stream.moreItems('<?php echo $result['itemId']; ?>'); return false;">
                                                        <button class="btn waves-effect waves-light <?php echo SITE_THEME; ?> more_link"><?php echo $LANG['action-more']; ?></button>
                                                    </a>
                                                </div>
                                            </div>

                                        <?php
                                        }

                                    } else {

                                        ?>

                                            <div class="row">
                                                <div class="col s12">
                                                    <div class="card blue-grey darken-1">
                                                        <div class="card-content white-text">
                                                            <span class="card-title"><?php echo $LANG['label-empty-list']; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php
                                    }
                                ?>

	        </div>

	        <div class="fixed-action-btn" style="bottom: 65px; right: 24px;">
                <a href="/profile/new_item.php" class="btn-floating btn-large <?php echo SITE_THEME; ?>">
                    <i class="material-icons">add</i>
                </a>
            </div>

        </div>
    </div>
</main>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
        ?>

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

            window.Stream || ( window.Stream = {} );

            Stream.moreItems = function (offset) {

                $.ajax({
                    type: 'POST',
                    url: '/stream.php',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.more_cont').remove();

                        if (response.hasOwnProperty('html')){

                            $("div.stream_cont").append(response.html);
                        }

                        items_loaded = response.items_loaded;
                        items_all = response.items_all;
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

        <script type="text/javascript" src="/js/chat.js"></script>

</body>
</html>

<?php

    function draw($item, $LANG, $helper)
    {
        ?>
                <div class="col s12 m4 item" data-id="<?php echo $item['id']; ?>">
                    <a href="/view_item.php/?id=<?php echo $item['id']; ?>">
                        <div class="card">
                            <div class="card-image">
                                <img class="item-img" src="<?php echo $item['imgUrl']; ?>">
                                <span class="card-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis"><?php echo $item['categoryTitle']; ?></span>
                            </div>
                            <div class="card-content">
                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis"><?php echo $item['itemTitle']; ?></p>
                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis"><?php echo $item['price']; ?><?php echo $LANG['label-currency']; ?></p>
                            </div>
                        </div>
                    </a>
                </div>

        <?php
    }
