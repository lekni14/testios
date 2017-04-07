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

    $query = '';

    $search = new search($dbo);
    $search->setRequestFrom(auth::getCurrentUserId());

    if (isset($_GET['query'])) {

        $query = isset($_GET['query']) ? $_GET['query'] : '';

        $query = helper::clearText($query);
        $query = helper::escapeText($query);
    }

    $items_all = $search->getItemsCount($query);
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;
        $query = isset($_POST['query']) ? $_POST['query'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $query = helper::clearText($query);
        $query = helper::escapeText($query);

        $result = $search->itemsQuery($query, $itemId, 20);

        $items_loaded = count($result['items']);
        $items_all = $result['itemsCount'];


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
                        <a href="javascript:void(0)" onclick="Search.moreItems('<?php echo $result['itemId']; ?>', '<?php  echo $query; ?>'); return false;">
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

    $account = new account($dbo, auth::getCurrentUserId());
    $account->setLastActive();
    unset($account);

    $page_id = "search";

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-search']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");

?>

<body>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>

<main class="content">

    <div class="container">
        <div class="row">
            <div class="col s12 m12 l12">

                <h2 class="header"><?php echo $LANG['page-search']; ?></h2>

                        <form method="get" action="/search.php">

                            <div class="row">
                                <div class="input-field col s8">
                                    <input type="text" class="validate" id="query" name="query" value="<?php echo stripslashes($query); ?>">
                                    <label for="query"><?php echo $LANG['label-search-prompt']; ?></label>
                                </div>

                                <div class="input-field col s2">
                                    <button type="submit" class="btn waves-effect waves-light btn-large <?php echo SITE_THEME; ?>" name=""><i class="material-icons">search</i></button>
                                </div>
                            </div>

                        </form>

                        <div class="col s12 m12 l12 left search_cont" style="padding-right: 0; padding-left: 0;">

                                <?php

                                    if (strlen($query) > 0) {

                                        $result = $search->itemsQuery($query, 0, 20);

                                        $items_all = $result['itemsCount'];
                                        $items_loaded = count($result['items']);

                                        ?>

                                            <div class="row">
                                                <div class="col s12">
                                                    <div class="card teal lighten-1">
                                                        <div class="card-content white-text">
                                                            <span class="card-title"><?php echo $LANG['label-search-result']; ?> <span id="search_count">(<?php echo $items_all; ?>)</span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php


                                        if ($items_loaded != 0) {

                                            foreach ($result['items'] as $key => $value) {

                                                draw($value, $LANG, $helper);
                                            }

                                            if ($items_all > 20) {

                                                ?>

                                                <div class="row more_cont">
                                                    <div class="col s12">
                                                        <a href="javascript:void(0)" onclick="Search.moreItems('<?php echo $result['itemId']; ?>', '<?php  echo $query; ?>'); return false;">
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
                                                                <span class="card-title"><?php echo $LANG['label-search-empty']; ?></span>
                                                            </div>
                                                        </div>
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
                                                            <span class="card-title"><?php echo $LANG['label-search-prompt']; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php
                                    }

                                ?>

                        </div>

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

            window.Search || ( window.Search = {} );

            Search.moreItems = function (offset, query) {

                $.ajax({
                    type: 'POST',
                    url: '/search.php',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded + "&query=" + query,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.more_cont').remove();

                        if (response.hasOwnProperty('html')){

                            $("div.search_cont").append(response.html);
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
                            </div>
                        </div>
                    </a>
                </div>

        <?php
    }
