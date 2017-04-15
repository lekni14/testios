<?php
include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

    header('Location: /');
    exit;
}


$action = $_GET['action'];
if($action=='list'){
  list_show();
}else if($action=='delete'){
  removefile();
}else if($action=='upload'){
  upload();
}
function con()
{
    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");
}
function list_show()
{
  con();
  if (isset($_GET['id'])) {

      $itemId = isset($_GET['id']) ? $_GET['id'] : 0;

      $itemId = helper::clearInt($itemId);

      $item = new items(con());
      $item->setRequestFrom(auth::getCurrentUserId());

      @$item = $item->info($itemId);

      if ($item['imagesCount'] > 0) {

          $images = new images(con());
          $images->setRequestFrom(auth::getCurrentUserId());

          $item['images'] = $images->get($itemId);

          unset($images);
      }

    if (array_key_exists("images", $item)) {
        $images = $item['images'];
        if (array_key_exists("items", $images)) {
          $images = $images['items'];
          $array = array();
          foreach ($images as $key => $value) {
            $array[]=array(
              'imgUrl' =>$value['previewImgUrl'],
              'name' =>basename($value['previewImgUrl']),
              "id" => $value['id'],
            );
          }
          // $images['size'] = filesize($images['imgUrl']);
          echo json_encode($array);
        }


    }
  }
}
function upload()
{
  if (isset($_FILES['file']['name'])) {

      $currentTime = time();
      $uploaded_file_ext = @pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

      if (@move_uploaded_file($_FILES['file']['tmp_name'], TEMP_PATH."{$currentTime}.".$uploaded_file_ext)) {

          $response = array();

          $imgLib = new imglib(con());
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
}
function removefile()
{
  $images = new images(con());
  $images->setRequestFrom(auth::getCurrentUserId());
  $images->remove($_POST['id']);
  // unlink("http://localhost/../items/img_34279g178g.png");
}
