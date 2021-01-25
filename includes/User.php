<?php

class User 
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

	public function getExistsMailId($emailId) {
		$stmt = $this->conn->prepare("SELECT * FROM hms_login WHERE email = ?");
		$stmt->bind_param("s",$emailId);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		if (mysqli_num_rows($result) == 0) {
			return true;
		} else {
			return false;
		}
		
	}

	// Function to Register the User
	public function addUser($userId,$jsonarray,$mailContent,$bodyrep)
	{

		$stmt = $this->conn->prepare("INSERT INTO hms_login 
										(userId, email, password, userType, createdBy, updatedBy) 
										VALUES(
											'".$userId."',
											'".$jsonarray['EmailId']."',
											'".md5($jsonarray['Password'])."',
											2,
											'".$jsonarray['GivenName']."',
											'".$jsonarray['GivenName']."'
										)");
		$stmt->execute();
		$stmt->close();

		$stmtUsers = $this->conn->prepare("INSERT INTO hms_users 
										(userId, firstName, lastName, email, dob, gender, mobileNo, userType, createdBy, updatedBy) 
										VALUES(
											'".$userId."',
											'".$jsonarray['SurName']."',
											'".$jsonarray['GivenName']."',
											'".$jsonarray['EmailId']."',
											'".$jsonarray['DOB']."',
											'".$jsonarray['gender']."',
											'".$jsonarray['MobileNo']."',
											2,
											'".$jsonarray['GivenName']."',
											'".$jsonarray['GivenName']."'
										)");
		$stmtUsers->execute();
		$stmtUsers->close();

		$stmtMail = $this->conn->prepare("INSERT INTO hms_mailstatus 
										(userId, toMail, subject, content, sendFlg, createdBy, updatedBy)
										VALUES(
											'".$userId."',
											'".$jsonarray['EmailId']."',
											'".$mailContent['subject']."',
											'".$bodyrep."',
											0,
											'".$jsonarray['GivenName']."',
											'".$jsonarray['GivenName']."'
										)");
		$stmtMail->execute();
		$stmtMail->close();

		return $stmt;
	}

	public function getUserList($arr) {
		if ($arr["Flg"] == "0") {
			$stmt = $this->conn->prepare("SELECT * FROM hms_users LEFT JOIN hms_login ON  hms_users.userId = hms_login.userId WHERE hms_users.userType = 2 AND hms_login.verifyFlg = 1 OR hms_login.verifyFlg = 0 ORDER BY hms_users.userId DESC");
		} elseif ($arr["Flg"] == "1") {
			$stmt = $this->conn->prepare("SELECT * FROM hms_users LEFT JOIN hms_login ON  hms_users.userId = hms_login.userId WHERE hms_users.userType = 2 AND hms_login.verifyFlg = 1 ORDER BY hms_users.userId DESC");
		} elseif ($arr["Flg"] == "2") {
			$stmt = $this->conn->prepare("SELECT * FROM hms_users LEFT JOIN hms_login ON  hms_users.userId = hms_login.userId WHERE hms_users.userType = 2 AND hms_login.verifyFlg = 0 ORDER BY hms_users.userId DESC");
		}
		$stmt->execute();
		$result = $stmt->get_result();
		
		$stmt->close();
		if (mysqli_num_rows($result) == 0) {
			return $result = array();
		} else {
			return $result;
		}
	}

}

?>