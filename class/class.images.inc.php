<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

class images extends db_connect
{
	private $requestFrom = 0;
    private $language = 'th';

	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    public function getAllCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM images");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxId()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM images");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function count($itemId)
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM images WHERE toItemId = (:toItemId) AND removeAt = 0");
        $stmt->bindParam(":toItemId", $itemId, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function add($itemId, $originImgUrl = "", $previewImgUrl = "", $imgUrl = "")
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (strlen($originImgUrl) == 0 && strlen($previewImgUrl) == 0 && strlen($imgUrl) == 0) {

            return $result;
        }

        $currentTime = time();
        $ip_addr = helper::ip_addr();
        $u_agent = helper::u_agent();

        $stmt = $this->db->prepare("INSERT INTO images (fromUserId, toItemId, originImgUrl, previewImgUrl, imgUrl, createAt, ip_addr, u_agent) value (:fromUserId, :toItemId, :originImgUrl, :previewImgUrl, :imgUrl, :createAt, :ip_addr, :u_agent)");
        $stmt->bindParam(":fromUserId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":toItemId", $itemId, PDO::PARAM_INT);
        $stmt->bindParam(":originImgUrl", $originImgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":previewImgUrl", $previewImgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":imgUrl", $imgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);
        $stmt->bindParam(":u_agent", $u_agent, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS,
                            "itemId" => $this->db->lastInsertId(),
                            "item" => $this->info($this->db->lastInsertId()));

            $item = new items($this->db);
            $item->recalculate($itemId);
            unset($item);
        }

        return $result;
    }

    public function removeAll($itemId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE images SET removeAt = (:removeAt) WHERE toItemId = (:toItemId)");
        $stmt->bindParam(":toItemId", $itemId, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function remove($imgId)
    {
        $result = array("error" => true);

        $imgInfo = $this->info($imgId);

        if ($imgInfo['error'] === true) {

            return $result;
        }

        if ($imgInfo['fromUserId'] != $this->requestFrom) {

            return $result;
        }

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE images SET removeAt = (:removeAt) WHERE id = (:imgId)");
        $stmt->bindParam(":imgId", $imgId, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array("error" => false);

            $item = new items($this->db);
            $item->recalculate($imgInfo['toItemId']);
            unset($item);
        }

        return $result;
    }

    public function restore($imgId)
    {
        $result = array("error" => true);

        $imgInfo = $this->info($imgId);

        if ($imgInfo['error'] === true) {

            return $result;
        }

        $stmt = $this->db->prepare("UPDATE images SET removeAt = 0 WHERE id = (:imgId)");
        $stmt->bindParam(":imgId", $imgId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array("error" => false);
        }

        return $result;
    }

    public function info($imgId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("SELECT * FROM images WHERE id = (:imgId) LIMIT 1");
        $stmt->bindParam(":imgId", $imgId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $time = new language($this->db, $this->language);

                $profile = new profile($this->db, $row['fromUserId']);
                $profileInfo = $profile->get();
                unset($profile);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "toItemId" => $row['toItemId'],
                                "fromUserId" => $row['fromUserId'],
                                "fromUserVerify" => $profileInfo['verify'],
                                "fromUserUsername" => $profileInfo['username'],
                                "fromUserFullname" => $profileInfo['fullname'],
                                "fromUserPhoto" => $profileInfo['lowPhotoUrl'],
                                "imgUrl" => $row['imgUrl'],
                                "previewImgUrl" => $row['previewImgUrl'],
                                "originImgUrl" => $row['originImgUrl'],
                                "createAt" => $row['createAt'],
                                "date" => date("Y-m-d H:i:s", $row['createAt']),
                                "timeAgo" => $time->timeAgo($row['createAt']),
                                "removeAt" => $row['removeAt']);
            }
        }

        return $result;
    }

    public function get($itemId)
    {
        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT id FROM images WHERE toItemId = (:toItemId) AND removeAt = 0 ORDER BY id DESC");
        $stmt->bindParam(':toItemId', $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $imgInfo = $this->info($row['id']);

                array_push($result['items'], $imgInfo);

                $result['itemId'] = $imgInfo['id'];

                unset($imgInfo);
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
}
