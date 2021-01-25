<?php

class Common 
{

	private $conn;

	function __construct()
	{
		require_once dirname(__FILE__) . '/Config.php';
		require_once dirname(__FILE__) . '/DbConnect.php';
		// opening db connection
		$db = new DbConnect();
		$this->conn = $db->connect();
	}

	// Function to get Mail Content
	public function getMailContent($mailContentId)
	{
		$stmt = $this->conn->prepare("SELECT * FROM hms_mailcontent WHERE mailId = ?");
		$stmt->bind_param("s",$mailContentId);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		return mysqli_fetch_array($result);
	}

	// Function to get Max User
	public function getMaxUserId($userType)
	{
		$stmt = $this->conn->prepare("SELECT max(userId) as userId FROM hms_login WHERE userType = ?");
		$stmt->bind_param("i",$userType);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		return mysqli_fetch_array($result);
	}
}