<?php

class MailStatus 
{

	private $conn;

	function __construct()
	{
		require_once dirname(__FILE__) . '/Config.php';
		require_once dirname(__FILE__) . '/DbConnect.php';
		require_once dirname(__FILE__) . '/Common.php';
		require_once dirname(__FILE__) . '/SendingMail.php';
		// opening db connection
		$db = new DbConnect();
		$this->conn = $db->connect();
	}

	public function getMailStatusList() {
		$stmt = $this->conn->prepare("SELECT * FROM hms_mailstatus LEFT JOIN hms_users ON hms_mailstatus.userId = hms_users.userId WHERE hms_mailstatus.delFlg = 0 ORDER BY hms_mailstatus.createdDateTime DESC");
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		if (mysqli_num_rows($result) == 0) {
			return $result =array();
		} else {
			return $result;
		}
	}
}

?>