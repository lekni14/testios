<?php
$ds          = DIRECTORY_SEPARATOR;  //1

$storeFolder = 'uploads';   //2

if (!empty($_FILES)) {
  // exit();
    // $tempFile = $_FILES['file']['tmp_name'];          //3
    //
    // $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
    //
    // $targetFile =  $targetPath. $_FILES['file']['name'];  //5
    //
    // move_uploaded_file($tempFile,$targetFile); //6

    $currentTime = time();
    $uploaded_file_ext = @pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    if (@move_uploaded_file($_FILES['file']['name'], TEMP_PATH."{$currentTime}.".$uploaded_file_ext)) {
        $response = array();

        $imgLib = new imglib($dbo);
        $response = $imgLib->createItemImg(TEMP_PATH."{$currentTime}.".$uploaded_file_ext, TEMP_PATH."{$currentTime}.".$uploaded_file_ext);

        if ($response['error'] === false) {

            $result = array("error" => false,
                            "normalPhotoUrl" => $response['imgUrl']);

            $imgUrl = $result['normalPhotoUrl'];
            $previewImgUrl = $result['normalPhotoUrl'];
        }

        unset($imgLib);
    }

}
?>
