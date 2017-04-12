<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

 // เพิ่ม item

    include_once($_SERVER['DOCUMENT_ROOT'] . "/core/init.inc.php");

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
        exit;
    }

    $title = "";
    $description = "";
    $content = "";
    $imgUrl = "";
    $previewImgUrl = "";
    $category_id = 0;
    $price = 0;
    $allow_comments = "";

    if (isset($_FILES['file']['name'])) {

        $currentTime = time();
        $uploaded_file_ext = @pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if (@move_uploaded_file($_FILES['file']['tmp_name'], TEMP_PATH."{$currentTime}.".$uploaded_file_ext)) {

            $response = array();

            $imgLib = new imglib($dbo);
            $response = $imgLib->createItemImg(TEMP_PATH."{$currentTime}.".$uploaded_file_ext, TEMP_PATH."{$currentTime}.".$uploaded_file_ext);

            if ($response['error'] === false) {

                $result = array("error" => false,
                                "normalPhotoUrl" => $response['imgUrl'],
                                "images_name" => $response['imgName']
                              );

                $imgUrl = $result['normalPhotoUrl'];
                $previewImgUrl = $result['normalPhotoUrl'];
            }

            unset($imgLib);
        }
        echo json_encode($result);
        exit();
    }

    if (!empty($_POST)) {
      // print_r($_POST['images']);
        $imgUrl = reset($_POST['images']);
      // echo $first_value;
        $authToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        $category_id = isset($_POST['category']) ? $_POST['category'] : 0;

        $price = isset($_POST['price']) ? $_POST['price'] : 0;

        $allow_comments = isset($_POST['allow_comments']) ? $_POST['allow_comments'] : '';

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

        $category_id = helper::clearInt($category_id);
        $price = helper::clearInt($price);

        if ($allow_comments === "on") {

            $allow_comments = 1;

        } else {

            $allow_comments = 0;
        }

        if ($authToken === helper::getAuthenticityToken()) {

            $item = new items($dbo);
            $item->setRequestFrom(auth::getCurrentUserId());
            // add($category, $title, $description, $content, $imgUrl, $previewImgUrl, $allowComments = 1, $price = 0, $postArea = "", $postCountry = "", $postCity = "", $postZipcode = "", $postLat = "0.000000", $postLng = "0.000000", $model, $year, $note)
            $result = $item->add($category_id, $title, $description, $content, $imgUrl, $previewImgUrl, $allow_comments, $price, $postArea = "", $postCountry = "", $postCity = "", $postZipcode = "", $postLat = "0.000000", $postLng = "0.000000", $model= null, $year= null, $note= null);
            
            if ($result['error'] === false) {
              foreach ($_POST['images'] as $key => $image) {

                $image = helper::clearText($image);
                $image = helper::escapeText($image);

                $images = new images($dbo);
                $images->setRequestFrom(auth::getCurrentUserId());
                $images->add($result['itemId'], $image, $image, $image);


              }

              header("Location: /view_item.php/?id=".$result['itemId']);
              exit;
            }
        }
    }

    $page_id = "new_item";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-create-item']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_header.inc.php");
?>

<body>

<?php

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_topbar.inc.php");
?>

<main class="content">
    <div class="container">
        <div class="row">
            <div class="col s12 m12 l12">

                <div class="card">
                    <div class="card-content">
                        <div class="row">
                            <div class="col s12">

                                <div class="row">
                                    <div class="col s12">
                                        <h4><?php echo $LANG['page-create-item']; ?></h4>
                                    </div>
                                </div>
                                <form method="post" action="/profile/new_item.php" enctype="multipart/form-data">

                                    <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                    <div class="row">

                                        <div class="input-field col s12">
                                            <select name="category">
                                                <option value="0" disabled selected><?php echo $LANG['label-category-choose']; ?></option>

                                                <?php

                                                    $category = new categories($dbo);
                                                    $result = $category->getList();
                                                    unset($category);

                                                    foreach ($result['items'] as $val) {

                                                        ?>
                                                        <option <?php if ($val['id'] == $category_id) echo "selected"; ?> value="<?php echo $val['id']; ?>"><?php echo $val['title']; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                            <label><?php echo $LANG['label-category']; ?></label>
                                        </div>

                                        <div class="input-field col s12">
                                            <input placeholder="<?php echo $LANG['label-title']; ?>" id="title" type="text" name="title" maxlength="255" class="validate" value="<?php echo stripslashes($title); ?>">
                                            <label for="title"><?php echo $LANG['label-title']; ?></label>
                                        </div>

                                        <div class="file-field input-field col s12">
                                          <!-- <input type="hidden" name="uploaded_file"> -->
                                          <div id="dZUpload" class="dropzone">
                                            <?php echo $LANG['label-image']; ?>
                                            <div class="dz-default dz-message"><?php echo $LANG['label-image-placeholder']; ?></div>
                                          </div>
                                            <!-- <div class="btn">
                                                <span><?php echo $LANG['label-image']; ?></span>
                                                <input type="file" name="uploaded_file">
                                            </div>
                                            <div class="file-path-wrapper">
                                              <div id="dZUpload" class="dropzone">
                                                Drop files here or click here
                                                  <div class="dz-default dz-message"></div>
                                              </div>
                                                <input class="file-path validate" type="text" placeholder="<?php echo $LANG['label-image-placeholder']; ?>">
                                            </div> -->
                                        </div>

                                        <div class="input-field col s12">
                                            <textarea id="textarea1" placeholder="<?php echo $LANG['label-description-placeholder']; ?>" class="materialize-textarea" name="content" rows="10" cols="80"><?php echo stripslashes($content); ?></textarea>

                                            <script type="text/javascript">

                                                $('#textarea1').trigger('autoresize');

                                                $(document).ready(function() {

                                                    $('select').material_select();
                                                });

                                            </script>

                                        </div>

                                        <div class="input-field col s3">
                                            <input placeholder="<?php echo $LANG['label-price']; ?>" id="price" type="text" name="price" maxlength="6" class="validate" value="<?php echo $price; ?>">
                                            <label for="price"><?php echo $LANG['label-price']; ?></label>
                                        </div>

                                        <div class="input-field col s12" style="margin-bottom: 20px;">
                                            <div>
                                                <input type="checkbox" id="allow_comments" checked="checked" name="allow_comments" />
                                                <label for="allow_comments"><?php echo $LANG['label-allow-comments']; ?></label>
                                            </div>
                                        </div>

                                        <div class="input-field col s12">
                                            <button type="submit" class="btn waves-effect waves-light" name="" ><?php echo $LANG['action-create']; ?></button>
                                        </div>

                                    </div>

                                </form>

                            </div>
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
        url: "new_item.php",
        addRemoveLinks: true,
        maxFiles: 4,
        success: function (file, response) {
          var json = jQuery.parseJSON(response)
          console.log(json)
          file.previewElement.querySelector("img").alt = json.images_name;
          file.previewElement.querySelector("img").src = json.normalPhotoUrl;
          addInput(json.normalPhotoUrl,json.images_name)
        },
        removedfile: function (file, response) {
          var name = file.previewElement.querySelector("img").getAttribute("alt");
          removeInput(name)
          // console.log(name);
          // $.ajax({
          //     type: 'POST',
          //     url: 'delete.php',
          //     data: "id="+name,
          //     dataType: 'html'
          // });
          var _ref;
          return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
        error: function (file, response) {
          console.log(response);
          file.previewElement.classList.add("dz-error");
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
