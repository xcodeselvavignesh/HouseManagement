<?php

class Home 
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

	// Function to Login the User
	public function loginUser($userId,$password,$flg)
	{
		$stmt = $this->conn->prepare("SELECT * FROM hms_login LEFT JOIN hms_users ON  hms_login.userId = hms_users.userId WHERE ? IN(hms_login.userId, hms_login.email) AND hms_login.password = ? AND hms_login.verifyFlg = ?");
		$stmt->bind_param("ssi",$userId,$password,$flg);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		if (mysqli_num_rows($result) == 0) {
			return $result = array();
		} else {
			return $result;
		}
	}

	// Function to Update Login Status
	public function updLoginFlg($userId,$flg)
	{
		$stmt = $this->conn->prepare("UPDATE hms_login SET loginStatus = '".$flg."' WHERE userId = '".$userId."'");
		$stmt->execute();
		$stmt->close();

		return $stmt;
	}

	/**
     * Reseting Password
     * @return Boolean
     */
	public function ResetPassword($jsonarray) {
		$email = mysqli_real_escape_string($this->conn, $jsonarray["Email"]);
		$sql = $this->conn->prepare("SELECT users.userId as UserID, users.firstName as FirstName FROM hms_login as credentials INNER JOIN hms_users as users ON users.userId = credentials.userId WHERE credentials.email = ?");
		$sql->bind_param("s", $email);
		$sql->execute();
		$result = $sql->get_result();
		$sql->close();
		if(mysqli_num_rows($result) == 0) {
			return 2;
		}	
		else {
			$this->conn->begin_transaction();
			$sqlArray = mysqli_fetch_array($result);
			$userName = $sqlArray["FirstName"];
			$userID = $sqlArray["UserID"];
			$length = 6;
			$randomPassword = $this->generateRandomPassword($length);
			$update_Password = $this->updatePassword($email, md5($randomPassword));
			if($update_Password) {
				$mailContentId = "MAIL0002";
				$dbCommon = new Common();
				$mailContent = $dbCommon->getMailContent($mailContentId);
				$bodyrep = $this->createMailContent($mailContent, $userID, $userName, $randomPassword);
				$dbMAil = new SendingMail();
				$mailSend = $dbMAil->sendIntimationMail($email,$mailContent['subject'],$bodyrep);
				if ($mailSend) {
					$this->conn->commit();
					return 0;
				} else {
					$this->conn->rollback();
					return 1;
				}
			}
			else {
				return 1;
			}
		}
	}

	/**
     * Update Random Password
     * @return Boolean
     */
	private function updatePassword($email, $randomPassword) {
		$sql = $this->conn->prepare("UPDATE hms_Login SET password = ? WHERE email = ?");
		$sql->bind_param("ss", $randomPassword, $email);
		$result = $sql->execute();
		$sql->close();
		if($result) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
     * Generating Random Password
     * @return String
     */
	private function generateRandomPassword($length) {
		return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);
	}

	/**
     * Creating Mail Content
     * @return Mail_Body(String)
     */
	private function createMailContent($mailContent, $userID, $userName, $randomPassword) {
		$header = $mailContent['header']." ".$userName.",\n";
		$content = $mailContent['content'];
		$content = str_replace('LLLLL', $userID, $content);
		$content = str_replace('PPPPP', $randomPassword, $content);
		$content = $content."\n";
		$footer = "Thanks & Regards"."\n"."Click Here To Visit Microbit.com Team";
		$bodyrep = $header.$content.$footer;
		return $bodyrep;
	}

	/**
     * Change Password
     * @return Boolean
     */
	public function ChangePassword($jsonarray) {
		$userId = mysqli_escape_string($this->conn, $jsonarray["UserID"]);
		$newPassword = mysqli_escape_string($this->conn, $jsonarray["NewPassword"]);
		$currentPassword = mysqli_escape_string($this->conn, $jsonarray["CurrentPassword"]);
		$currentPassword = md5($currentPassword);
		$newPassword = md5($newPassword);
		if($this->checkValidPassword($currentPassword, $userId)) {
			if($this->setNewPassword($newPassword, $userId)) {
				return true;
			}
			else {
				return false;
			}
		}
		else  {
			return false;
		}
	}

	/**
	 * Check Current Password is Valid
	 * @return Boolean
	 */
	private function checkValidPassword($currentPassword, $userId) {
		$sql = $this->conn->prepare("SELECT * FROM hms_login WHERE password = ? AND userId = ?");
		$sql->bind_param("ss", $currentPassword, $userId);
		$sql->execute();
		$result = $sql->get_result();
		$sql->close();
		if(mysqli_num_rows($result) == 0) {
			return false;
		}	
		else {
			return true;
		}
	}

	private function setNewPassword($newPassword, $userId) {
		$sql = $this->conn->prepare("UPDATE hms_login SET password = ? WHERE userId = ?");
		$sql->bind_param("ss", $newPassword, $userId);
		$result = $sql->execute();
		$sql->close();
		if($result) {
			return true;
		}
		else {
			return false;
		}
	}

}

?>