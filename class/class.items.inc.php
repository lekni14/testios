<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

class items extends db_connect
{
	private $requestFrom = 0;
    private $language = 'th';
    private $profileId = 0;

	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    public function getAllCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM items");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxIdLikes()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM likes");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxIdItems()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM items");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function count()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM items WHERE fromUserId = (:fromUserId) AND removeAt = 0");
        $stmt->bindParam(":fromUserId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function add($category, $title, $description, $content, $imgUrl, $previewImgUrl, $allowComments = 1, $price = 0, $postArea = "", $postCountry = "", $postCity = "", $postZipcode = "", $postLat = "0.000000", $postLng = "0.000000", $model, $year, $note)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (strlen($title) == 0) {

            return $result;
        }

        if (strlen($imgUrl) == 0) {

            return $result;
        }

        if (strlen($content) == 0) {

            return $result;
        }

        if ($category == 0) {

            return $result;
        }

        if ($price == 0) {

            return $result;
        }

        $currentTime = time();
        $ip_addr = helper::ip_addr();
        $u_agent = helper::u_agent();

        $stmt = $this->db->prepare("INSERT INTO items (allowComments, fromUserId, category, itemTitle, itemDesc, itemContent, imgUrl, previewImgUrl, price, area, country, city, zipcode, lat, lng, createAt, ip_addr, u_agent, model, year, note) value (:allowComments, :fromUserId, :category, :itemTitle, :itemDesc, :itemContent, :imgUrl, :previewImgUrl, :price, :area, :country, :city, :zipcode, :lat, :lng, :createAt, :ip_addr, :u_agent, :model, :year, :note)");
        $stmt->bindParam(":allowComments", $allowComments, PDO::PARAM_INT);
        $stmt->bindParam(":fromUserId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":category", $category, PDO::PARAM_INT);
        $stmt->bindParam(":itemTitle", $title, PDO::PARAM_STR);
        $stmt->bindParam(":model", $model, PDO::PARAM_STR);
        $stmt->bindParam(":year", $year, PDO::PARAM_STR);
        $stmt->bindParam(":note", $note, PDO::PARAM_STR);
        $stmt->bindParam(":itemDesc", $description, PDO::PARAM_STR);
        $stmt->bindParam(":itemContent", $content, PDO::PARAM_STR);
        $stmt->bindParam(":imgUrl", $imgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":previewImgUrl", $previewImgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":price", $price, PDO::PARAM_INT);
        $stmt->bindParam(":area", $postArea, PDO::PARAM_STR);
        $stmt->bindParam(":country", $postCountry, PDO::PARAM_STR);
        $stmt->bindParam(":city", $postCity, PDO::PARAM_STR);
        $stmt->bindParam(":zipcode", $postZipcode, PDO::PARAM_STR);
        $stmt->bindParam(":lat", $postLat, PDO::PARAM_STR);
        $stmt->bindParam(":lng", $postLng, PDO::PARAM_STR);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);
        $stmt->bindParam(":u_agent", $u_agent, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS,
                            "itemId" => $this->db->lastInsertId(),
                            "item" => $this->info($this->db->lastInsertId()));

            if ($this->requestFrom != 0) {

                $account = new account($this->db, $this->requestFrom);
                $account->updateCounters();
                unset($account);
            }

            if ($category != 0) {

                $cat = new categories($this->db);
                $cat->recalculate($category);
                unset($cat);
            }
        }

        return $result;
    }

    public function deleteAllByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT id FROM items WHERE fromUserId = (:fromUserId) AND removeAt = 0");
        $stmt->bindParam(':fromUserId', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $this->remove($row['id']);
            }
        }
    }

    public function remove($itemId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $itemInfo = $this->info($itemId);

        if ($itemInfo['error'] === true) {

            return $result;
        }

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE items SET removeAt = (:removeAt) WHERE id = (:itemId)");
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            // remove all notifications by likes and comments

            $stmt2 = $this->db->prepare("DELETE FROM notifications WHERE itemId = (:itemId)");
            $stmt2->bindParam(":itemId", $itemId, PDO::PARAM_INT);
            $stmt2->execute();

            //remove all comments to item

            $stmt3 = $this->db->prepare("UPDATE comments SET removeAt = (:removeAt) WHERE itemId = (:itemId)");
            $stmt3->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);
            $stmt3->bindParam(":itemId", $itemId, PDO::PARAM_INT);
            $stmt3->execute();

            //remove all likes to item

            $stmt = $this->db->prepare("UPDATE likes SET removeAt = (:removeAt) WHERE itemId = (:itemId) AND removeAt = 0");
            $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
            $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);
            $stmt->execute();

            $cat = new categories($this->db);
            $cat->recalculate($itemInfo['category']);

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        $this->recalculate($itemId);

        return $result;
    }

    public function restore($itemId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $itemInfo = $this->info($itemId);

        if ($itemInfo['error'] === true) {

            return $result;
        }

        $stmt = $this->db->prepare("UPDATE items SET removeAt = 0 WHERE id = (:itemId)");
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function edit($itemId, $category, $title, $imgUrl, $content, $allowComments, $price, $model, $year, $note)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (strlen($title) == 0) {

            return $result;
        }

        if (strlen($imgUrl) == 0) {

            return $result;
        }

        if (strlen($content) == 0) {

            return $result;
        }

        if ($category == 0) {

            return $result;
        }

        if ($price == 0) {

            return $result;
        }

        $stmt = $this->db->prepare("UPDATE items SET model = (:model), year = (:year), note = (:note), allowComments = (:allowComments), category = (:category), itemTitle = (:itemTitle), itemContent = (:itemContent), imgUrl = (:imgUrl), price = (:price), moderatedAt = 0, moderatedId = 0 WHERE id = (:itemId)");
        $stmt->bindParam(":allowComments", $allowComments, PDO::PARAM_INT);
        $stmt->bindParam(":category", $category, PDO::PARAM_INT);
        $stmt->bindParam(":itemTitle", $title, PDO::PARAM_STR);
        $stmt->bindParam(":itemContent", $content, PDO::PARAM_STR);
        $stmt->bindParam(":imgUrl", $imgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":model", $model, PDO::PARAM_STR);
        $stmt->bindParam(":year", $year, PDO::PARAM_STR);
        $stmt->bindParam(":note", $note, PDO::PARAM_STR);
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
        $stmt->bindParam(":price", $price, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function like($itemId, $fromUserId)
    {
        $account = new account($this->db, $fromUserId);
        $account->setLastActive();
        unset($account);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $itemInfo = $this->info($itemId);

        if ($itemInfo['error'] === true) {

            return $result;
        }

        if ($itemInfo['removeAt'] != 0) {

            return $result;
        }

        if ($this->is_like_exists($itemId, $fromUserId)) {

            $removeAt = time();

            $stmt = $this->db->prepare("UPDATE likes SET removeAt = (:removeAt) WHERE itemId = (:itemId) AND fromUserId = (:fromUserId) AND removeAt = 0");
            $stmt->bindParam(":fromUserId", $fromUserId, PDO::PARAM_INT);
            $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
            $stmt->bindParam(":removeAt", $removeAt, PDO::PARAM_INT);
            $stmt->execute();

            $notify = new notify($this->db);
            $notify->removeNotify($itemInfo['fromUserId'], $fromUserId, NOTIFY_TYPE_LIKE, $itemId);
            unset($notify);

        } else {

            $createAt = time();
            $ip_addr = helper::ip_addr();

            $stmt = $this->db->prepare("INSERT INTO likes (toUserId, fromUserId, itemId, createAt, ip_addr) value (:toUserId, :fromUserId, :itemId, :createAt, :ip_addr)");
            $stmt->bindParam(":toUserId", $itemInfo['fromUserId'], PDO::PARAM_INT);
            $stmt->bindParam(":fromUserId", $fromUserId, PDO::PARAM_INT);
            $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
            $stmt->bindParam(":createAt", $createAt, PDO::PARAM_INT);
            $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);
            $stmt->execute();
        }

        $this->recalculate($itemId);

        $item_info = $this->info($itemId);

        if ($item_info['fromUserId'] != $this->requestFrom) {

            $account = new account($this->db, $item_info['fromUserId']);
            $account->updateCounters();
            unset($account);
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "likesCount" => $item_info['likesCount'],
                        "myLike" => $item_info['myLike']);

        return $result;
    }

    private function getLikesCount($itemId)
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM likes WHERE itemId = (:itemId) AND removeAt = 0");
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function is_like_exists($itemId, $fromUserId)
    {
        $stmt = $this->db->prepare("SELECT id FROM likes WHERE fromUserId = (:fromUserId) AND itemId = (:itemId) AND removeAt = 0 LIMIT 1");
        $stmt->bindParam(":fromUserId", $fromUserId, PDO::PARAM_INT);
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return true;
        }

        return false;
    }

//    public function viewCount($itemId)
//        {
//
//            $stmt = $this->db->prepare("UPDATE items SET viewsCount = viewsCount + (:viewsCount) WHERE itemId = (:itemId)");
//            $stmt->bindParam(":viewsCount", $viewsCount, PDO::PARAM_INT);
//            $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
//            $stmt->execute(array(':viewsCount' => 1));
//          	return $result;
//        }

//    public function viewCount($itemId, $fromUser) {
//
//            $result = array("error" => true,
//                            "error_code" => ERROR_UNKNOWN);
//
//            $currentTime = time();
//
//            $stmt = $this->db->prepare("UPDATE items SET viewsCount = (:viewsCount) WHERE itemId = (:itemId) AND fromUserId = (:fromUserId) AND removeAt = 0 AND viewsCount = 0");
//            $stmt->bindParam(":viewsCount", $currentTime, PDO::PARAM_INT);
//            $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
//            $stmt->bindParam(":fromUserId", $fromUser, PDO::PARAM_INT);
//
//            if ($stmt->execute()) {
//
//                $result = array("error" => false,
//                                "error_code" => ERROR_SUCCESS);
//            }
//
//            return $result;
//        }

    public function recalculate($itemId) {

        $comments_count = 0;
        $likes_count = 0;
        $rating = 0;
        $images_count = 0;

        $modifyAt = time();

        $likes_count = $this->getLikesCount($itemId);

        $comments = new comments($this->db);
        $comments_count = $comments->count($itemId);
        unset($comments);

        $images = new images($this->db);
        $images_count = $images->count($itemId);
        unset($images);

        $rating = $likes_count + $comments_count;

        $stmt = $this->db->prepare("UPDATE items SET imagesCount = (:imagesCount), likesCount = (:likesCount), commentsCount = (:commentsCount), rating = (:rating), modifyAt = (:modifyAt) WHERE id = (:itemId)");
        $stmt->bindParam(":imagesCount", $images_count, PDO::PARAM_INT);
        $stmt->bindParam(":likesCount", $likes_count, PDO::PARAM_INT);
        $stmt->bindParam(":commentsCount", $comments_count, PDO::PARAM_INT);
        $stmt->bindParam(":rating", $rating, PDO::PARAM_INT);
        $stmt->bindParam(":modifyAt", $modifyAt, PDO::PARAM_INT);
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
        $stmt->execute();

        $account = new account($this->db, $this->requestFrom);
        $account->updateCounters();
        unset($account);
    }

    public function info($itemId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = (:itemId) LIMIT 1");
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $time = new language($this->db, $this->language);

                $myLike = false;

                if ($this->requestFrom != 0) {

                    if ($this->is_like_exists($itemId, $this->requestFrom)) {

                        $myLike = true;
                    }
                }

                if ($row['fromUserId'] != 0) {

                    $profile = new profile($this->db, $row['fromUserId']);
                    $profileInfo = $profile->get();
                    unset($profile);

                } else {

                    $profileInfo = array("username" => "",
                                         "fullname" => "",
                                         "lowPhotoUrl" => "");
                }

                $category = new categories($this->db);
                $categoryInfo = $category->info($row['category']);
                unset($category);

               // Add New


                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "category" => $row['category'],
                                "price" => $row['price'],
                                "categoryTitle" => $categoryInfo['title'],
                                "fromUserId" => $row['fromUserId'],
                                "fromUserUsername" => $profileInfo['username'],
                                "fromUserFullname" => $profileInfo['fullname'],
                                "fromUserPhone" => $profileInfo['phone'],
                                "fromUserPhoto" => $profileInfo['lowPhotoUrl'],
                                "fromUserVerify" => $profileInfo['UserVerify'],
                                "Verify" => $profileInfo['verify'],
                                "profilelat" => $profileInfo['lat'],
                                "profilelng" => $profileInfo['lng'],
                                "fromUserOnline" => $profileInfo['online'],
                                "lastAuthorize" => $profileInfo['last_authorize'],
                                "lastAuthorizeDate" => date("Y-m-d H:i:s", $profileInfo['lastAuthorize']),
                                "lastAuthorizeTimeAgo" => $time->timeAgo($profileInfo['lastAuthorize']),
                                "itemTitle" => htmlspecialchars_decode(stripslashes($row['itemTitle'])),
                                "itemDesc" => htmlspecialchars_decode(stripslashes($row['itemDesc'])),
                                "itemContent" => stripslashes($row['itemContent']),
                                "area" => htmlspecialchars_decode(stripslashes($row['area'])),
                                "country" => htmlspecialchars_decode(stripslashes($row['country'])),
                                "city" => htmlspecialchars_decode(stripslashes($row['city'])),
                                "zipcode" => htmlspecialchars_decode(stripslashes($row['zipcode'])),
                                "model" => htmlspecialchars_decode(stripslashes($row['model'])),
                                "year" => htmlspecialchars_decode(stripslashes($row['year'])),
                                "note" => htmlspecialchars_decode(stripslashes($row['note'])),
//                                "itemsCount" => $this->getAllCount(),
                                "lat" => $row['lat'],
                                "lng" => $row['lng'],
                                "previewImgUrl" => $row['previewImgUrl'],
                                "imgUrl" => $row['imgUrl'],
                                "allowComments" => $row['allowComments'],
                                "rating" => $row['rating'],
                                "commentsCount" => $row['commentsCount'],
                                "likesCount" => $row['likesCount'],
                                "imagesCount" => $row['imagesCount'],
                                "viewsCount" => $row['viewsCount'],
                                "myLike" => $myLike,
                                "createAt" => $row['createAt'],
                                "modifyAt" => $row['modifyAt'],
                                "date" => date("Y-m-d H:i:s", $row['createAt']),
                                "modify" => date("Y-m-d H:i:s", $row['modifyAt']),
                                "PosttimeAgo" => $time->timeAgo($row['createAt']),
                                "fb_page" => stripcslashes($profileInfo['fb_page']),
                                "instagram_page" => stripcslashes($profileInfo['my_page']),
                                "status" => stripcslashes($profileInfo['status']),
                                "removeAt" => $row['removeAt']);
            }
        }

        return $result;
    }

    public function get($profileId, $itemId = 0)
    {
        if ($itemId == 0) {

            $itemId = $this->getMaxIdItems();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT id FROM items WHERE fromUserId = (:fromUserId) AND removeAt = 0 AND id < (:itemId) ORDER BY id DESC LIMIT 20");
        $stmt->bindParam(':fromUserId', $profileId, PDO::PARAM_INT);
        $stmt->bindParam(':itemId', $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $itemInfo = $this->info($row['id']);

                array_push($result['items'], $itemInfo);

                $result['itemId'] = $itemInfo['id'];

                unset($itemInfo);
            }
        }

        return $result;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }

    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    public function getProfileId()
    {
        return $this->profileId;
    }
}
