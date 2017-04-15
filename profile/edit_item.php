<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

 // แก้ไข item

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
        exit;
    }

    $title = "";
    $description = "";
    $content = "";
    $imgUrl = "";
    $previewImgUrl = "";
    $category = 0;
    $price = 0;

    if (isset($_GET['id'])) {

        $itemId = isset($_GET['id']) ? $_GET['id'] : 0;

        $itemId = helper::clearInt($itemId);

        $item = new items($dbo);
        $itemInfo = $item->info($itemId);

        if ($itemInfo['error'] === true || $itemInfo['removeAt'] != 0 || $itemInfo['fromUserId'] != auth::getCurrentUserId()) {

            header("Location: /");
            exit;
        }

    } else {

        header("Location: /");
        exit;
    }

    if (!empty($_POST)) {

        $imgUrl = isset($_POST['imgUrl']) ? $_POST['imgUrl'] : '';

        $imgUrl = helper::clearText($imgUrl);
        $imgUrl = helper::escapeText($imgUrl);
    }

    // if (strlen($imgUrl) == 0 && isset($_FILES['uploaded_file']['name'])) {
    //
    //     $currentTime = time();
    //     $uploaded_file_ext = @pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);
    //
    //     if (@move_uploaded_file($_FILES['uploaded_file']['tmp_name'], TEMP_PATH."{$currentTime}.".$uploaded_file_ext)) {
    //
    //         $response = array();
    //
    //         $imgLib = new imglib($dbo);
    //         $response = $imgLib->createItemImg(TEMP_PATH."{$currentTime}.".$uploaded_file_ext, TEMP_PATH."{$currentTime}.".$uploaded_file_ext);
    //
    //         if ($response['error'] === false) {
    //
    //             $result = array("error" => false,
    //                             "normalPhotoUrl" => $response['imgUrl']);
    //
    //             $imgUrl = $result['normalPhotoUrl'];
    //             $previewImgUrl = $result['normalPhotoUrl'];
    //         }
    //
    //         unset($imgLib);
    //     }
    // }

    if (!empty($_POST)) {

      $imgUrl = reset($_POST['images']);

        $authToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        $category = isset($_POST['category']) ? $_POST['category'] : 0;
        $allow_comments = isset($_POST['allow_comments']) ? $_POST['allow_comments'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : 0;

        $title = helper::clearText($title);
        $title = helper::escapeText($title);

        $description = helper::clearText($description);
        $description = helper::escapeText($description);

        $description = $title;

        $imgUrl = helper::clearText($imgUrl);
        $imgUrl = helper::escapeText($imgUrl);

        $previewImgUrl = helper::clearText($previewImgUrl);
        $previewImgUrl = helper::escapeText($previewImgUrl);

        $content = helper::clearText($content);

        $content = preg_replace( "/[\r\n]+/", "<br>", $content); //replace all new lines to one new line
        $content = preg_replace('/\s+/', ' ', $content);        //replace all white spaces to one space

        $content = helper::escapeText($content);

        $category = helper::clearInt($category);
        $price = helper::clearInt($price);

        if ($allow_comments === "on") {

            $allow_comments = 1;

        } else {

            $allow_comments = 0;
        }

        if ($authToken === helper::getAuthenticityToken()) {

            $item = new items($dbo);

            $result = $item->edit($itemId, $category, $title, $imgUrl, $content, $allow_comments, $price);

            if ($result['error'] === false) {
              // print_r($result);
              // exit();
              foreach ($_POST['images'] as $key => $image) {

                $image = helper::clearText($image);
                $image = helper::escapeText($image);

                $images = new images($dbo);
                $images->setRequestFrom(auth::getCurrentUserId());
                $images->add($itemId, $image, $image, $image);


              }

                header("Location: /view_item.php/?id=".$itemId);
                exit;
            }
        }
    }

    $page_id = "edit_item";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-edit-item']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_header.inc.php");
?>

<body>

<?php

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_topbar.inc.php");
?>

<main class="content">
    <div class="row">
        <div class="col s12 m12 l12">

            <div class="card">
                <div class="card-content">
                    <div class="row">
                        <div class="col s12">

                            <div class="row">
                                <div class="col s6">
                                    <h4><?php echo $LANG['page-edit-item']; ?></h4>
                                </div>
                            </div>

                            <form method="post" action="/profile/edit_item.php/?id=<?php echo $itemInfo['id']; ?>" enctype="multipart/form-data">

                                <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
                                <input type="hidden" name="imgUrl" value="<?php echo $itemInfo['imgUrl']; ?>">

                                <div class="row">

                                    <div class="input-field col s12">
                                        <select name="category">

                                            <?php

                                                $category = new categories($dbo);
                                                $result = $category->getList();
                                                unset($category);

                                                foreach ($result['items'] as $val) {

                                                    ?>
                                                    <option value="<?php echo $val['id']; ?>" <?php if ($itemInfo['category'] == $val['id']) { echo "selected"; } ?>><?php echo $val['title']; ?></option>
                                                    <?php
                                                }
                                            ?>
                                        </select>
                                        <label><?php echo $LANG['label-category']; ?></label>
                                    </div>

                                    <div class="input-field col s12">
                                        <input placeholder="<?php echo $LANG['label-title']; ?>" id="title" type="text" name="title" maxlength="255" class="validate" value="<?php echo $itemInfo['itemTitle']; ?>">
                                        <label for="title"><?php echo $LANG['label-title']; ?></label>
                                    </div>

                                    <div class="input-field col s12 m7 image-preview">
                                      <div id="dZUpload" class="dropzone">
                                        <?php echo $LANG['label-image']; ?>
                                        <div class="dz-default dz-message"><?php echo $LANG['label-image-placeholder']; ?></div>
                                      </div>
                                        <!-- <div class="card">
                                            <div class="card-image">
                                                <img src="<?php echo $itemInfo['imgUrl']; ?>">
                                            </div>
                                            <div class="card-action">
                                                <a href="javascript:void(0)" onclick="Action.remove(); return false;"><?php echo $LANG['action-remove']; ?></a>
                                            </div>
                                        </div> -->
                                    </div>

                                    <div class="file-field input-field col s12" style="display: none">
                                        <div class="btn">
                                            <span><?php echo $LANG['label-image']; ?></span>
                                            <input type="file" name="uploaded_file">
                                        </div>
                                        <div class="file-path-wrapper">
                                            <input class="file-path validate" type="text" placeholder="<?php echo $LANG['label-image-placeholder']; ?>">
                                        </div>
                                    </div>

                                    <div class="input-field col s12">
                                        <?php

                                            $itemInfo['itemContent'] = strip_tags($itemInfo['itemContent']);
                                        ?>
                                        <textarea id="textarea1" placeholder="<?php echo $LANG['label-description-placeholder']; ?>" class="materialize-textarea" name="content" maxlength="1000" rows="10" cols="80"><?php echo $itemInfo['itemContent']; ?></textarea>

                                        <script type="text/javascript">

                                            $('#textarea1').trigger('autoresize');

                                            $(document).ready(function() {

                                                $('select').material_select();
                                            });

                                            window.Action || ( window.Action = {} );

                                            Action.remove = function () {

                                                $('input[name=imgUrl]').val("");
                                                $('div.file-field').show();
                                                $('div.image-preview').hide();
                                            };

                                        </script>

                                    </div>

                                    <div class="input-field col s3">
                                        <input placeholder="<?php echo $LANG['label-price']; ?>" id="price" type="text" name="price" maxlength="6" class="validate" value="<?php echo $itemInfo['price']; ?>">
                                        <label for="price"><?php echo $LANG['label-price']; ?></label>
                                    </div>

                                    <div class="input-field col s12" style="margin-bottom: 20px;">
                                        <div>
                                            <input type="checkbox" id="allow_comments" <?php if ($itemInfo['allowComments'] == 1) echo "checked=\"checked\"" ?> name="allow_comments" />
                                            <label for="allow_comments"><?php echo $LANG['label-allow-comments']; ?></label>
                                        </div>
                                    </div>

                                    <div class="input-field col s12">
                                        <button type="submit" class="btn waves-effect waves-light" name="" ><?php echo $LANG['action-save']; ?></button>
                                    </div>

                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_footer.inc.php");
?>

<script type="text/javascript">
$(document).ready(function () {
    Dropzone.autoDiscover = false;
    $("#dZUpload").dropzone({
        url: "/profile/upload.php?action=upload",
        addRemoveLinks: true,
        maxFiles: 4,
        success: function (file, response) {
          var json = jQuery.parseJSON(response)
          file.previewElement.querySelector("img").alt = json.images_name;
          file.previewElement.querySelector("img").src = json.normalPhotoUrl;
          addInput(json.normalPhotoUrl,json.images_name)
        },
        removedfile: function (file, response) {
          // var name = file.previewElement.querySelector("id").getAttribute("alt");
          var id = 'id='+$(file.previewElement).attr('id');
          $.ajax({
                    url: '/profile/upload.php?action=delete',
                    data:id,
                    type: 'post'
                }).done(function (result) {//console.log(result.filename);
                  console.log(result)
                });
          var _ref;
          return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
        error: function (file, response) {
          console.log(response);
          file.previewElement.classList.add("dz-error");
        },
        init: function() {
          var thisDropzone = this;
          $.getJSON('/profile/upload.php?action=list&id=<?=$_GET['id']?>', function(response) {
            // var json = jQuery.parseJSON(response);
            $.each(response, function(key,value){
              console.log(value)
                var mockFile = { name: value.name, size: 1000 };
                thisDropzone.emit("addedfile", mockFile);
                thisDropzone.createThumbnailFromUrl(mockFile, value.imgUrl);
                thisDropzone.emit("complete", mockFile);
                $(mockFile.previewElement).prop('id', value.id);
            });
          });
        }
    });
});
function addInput(normalPhotoUrl,images_name) {

  $('<input>').attr({ type: 'hidden', id: cut(images_name), name: 'images['+cut(images_name)+']', value: normalPhotoUrl}).appendTo('form');
}
function removeInput(images_name) {
  console.log(images_name);
  $("#"+cut(images_name)).remove();
  // $('<input>').attr({ type: 'hidden', id: images_name, name: 'bar', value: normalPhotoUrl}).appendTo('form');
}
function cut(str)
{
  var res = str.split(".");
  return res[0];
}
</script>

</body>
</html>
