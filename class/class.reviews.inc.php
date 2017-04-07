<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

class reviews extends db_connect
{

	private $requestFrom = 0;
    private $language = 'th';

	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    public function allReviewsCount()
    {
        $stmt = $this->db->prepare("SELECT max(id) FROM reviews");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getAllCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM reviews WHERE removeAt = 0");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function count($toUserId)
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM reviews WHERE toUserId = (:toUserId) AND removeAt = 0");
        $stmt->bindParam(":toUserId", $toUserId, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function create($toUserId, $review, $rank = 0, $notifyId = 0)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (strlen($review) == 0) {

            return $result;
        }

        $currentTime = time();
        $ip_addr = helper::ip_addr();
        $u_agent = helper::u_agent();

        $stmt = $this->db->prepare("INSERT INTO reviews (fromUserId, toUserId, review, rank, createAt, notifyId, ip_addr, u_agent) value (:fromUserId, :toUserId, :review, :rank, :createAt, :notifyId, :ip_addr, :u_agent)");
        $stmt->bindParam(":fromUserId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":toUserId", $toUserId, PDO::PARAM_INT);
        $stmt->bindParam(":rank", $rank, PDO::PARAM_INT);
        $stmt->bindParam(":review", $review, PDO::PARAM_STR);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":notifyId", $notifyId, PDO::PARAM_INT);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);
        $stmt->bindParam(":u_agent", $u_agent, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS,
                            "itemId" => $this->db->lastInsertId(),
                            "review" => $this->info($this->db->lastInsertId()));

            $account = new account($this->db, $toUserId);

            if ($account->getAllowReviewsGCM() == 1) {

                $gcm = new gcm($this->db, $toUserId);
                $gcm->setData(GCM_NOTIFY_REVIEW, "You have a new review.", 0);
                $gcm->send();
            }

            $notify = new notify($this->db);
            $notifyId = $notify->createNotify($toUserId, $this->requestFrom, NOTIFY_TYPE_REVIEW, 0);
            unset($notify);

            $this->setNotifyId($result['itemId'], $notifyId);

            $account->updateCounters();

            unset($account);
        }

        unset($items);

        return $result;
    }

    private function setNotifyId($itemId, $notifyId)
    {
        $stmt = $this->db->prepare("UPDATE reviews SET notifyId = (:notifyId) WHERE id = (:itemId)");
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
        $stmt->bindParam(":notifyId", $notifyId, PDO::PARAM_INT);

        $stmt->execute();
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

        $stmt = $this->db->prepare("UPDATE reviews SET removeAt = (:removeAt) WHERE id = (:itemId)");
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $notify = new notify($this->db);
            $notify->remove($itemInfo['notifyId']);
            unset($notify);

            $account = new account($this->db, $itemInfo['toUserId']);
            $account->updateCounters();
            unset($account);

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function removeAll($toUserId) {

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE reviews SET removeAt = (:removeAt) WHERE toUserId = (:toUserId)");
        $stmt->bindParam(":toUserId", $toUserId, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);
    }

    public function info($itemId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = (:itemId) LIMIT 1");
        $stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $time = new language($this->db, $this->language);

                $profile = new profile($this->db, $row['fromUserId']);
                $fromUserId = $profile->get();
                unset($profile);

                $lowPhotoUrl = "";

                if (strlen($fromUserId['lowPhotoUrl']) != 0) {

                    $lowPhotoUrl = $fromUserId['lowPhotoUrl'];
                }

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "review" => htmlspecialchars_decode(stripslashes($row['review'])),
                                "answer" => htmlspecialchars_decode(stripslashes($row['answer'])),
                                "fromUserId" => $row['fromUserId'],
                                "fromUserState" => $fromUserId['state'],
                                "fromUserUsername" => $fromUserId['username'],
                                "fromUserFullname" => $fromUserId['fullname'],
                                "fromUserPhotoUrl" => $lowPhotoUrl,
                                "toUserId" => $row['toUserId'],
                                "rank" => $row['rank'],
                                "createAt" => $row['createAt'],
                                "notifyId" => $row['notifyId'],
                                "timeAgo" => $time->timeAgo($row['createAt']));
            }
        }

        return $result;
    }

    public function get($toUserId, $itemId = 0)
    {
        if ($itemId == 0) {

            $itemId = $this->allReviewsCount();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT id FROM reviews WHERE toUserId = (:toUserId) AND id < (:itemId) AND removeAt = 0 ORDER BY id DESC LIMIT 20");
        $stmt->bindParam(':toUserId', $toUserId, PDO::PARAM_INT);
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

    public function stream($itemId = 0)
    {
        if ($itemId == 0) {

            $itemId = $this->allReviewsCount();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT id FROM reviews WHERE removeAt = 0 AND id < (:itemId) ORDER BY id DESC LIMIT 20");
        $stmt->bindParam(':itemId', $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $info = $this->info($row['id']);

                    array_push($result['items'], $info);

                    $result['itemId'] = $info['id'];

                    unset($info);
                }
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
