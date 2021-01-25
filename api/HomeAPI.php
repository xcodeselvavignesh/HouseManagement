<?php
//creating reponse array
$response = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// including the Mail File
	require_once '../includes/Common.php';
	// including the Home Add file
	require_once '../includes/Home.php';
	// including the Mail File
	require_once '../includes/SendingMail.php';
	// Mail Value
	$mailContentId = "MAIL0001";
	//getting values
	$jsonarray = json_decode(file_get_contents('php://input'), true);
	//Instantiating Home Class
	$db = new Home();
	if($jsonarray["ProcessName"] == "Login") {
		$userId = $jsonarray["userId"];
		$password = md5($jsonarray["password"]);
		$flg = 1;
		$loginUser[] = $db->loginUser($userId,$password,$flg);
		if (!empty($loginUser[0])) {
			foreach ($loginUser as $item) {
				while ($row = $item->fetch_assoc()) {
					array_push($response, $row);
					$db->updLoginFlg($row['userId'],1);
				}
			}
			setResponseMessage(true);
		} else {
			setResponseMessage(false);
		}
	}
	else if($jsonarray["ProcessName"] == "ResetPassword") {
		$result = $db->ResetPassword($jsonarray);
		if ($result == 0 || $result == 2) {
			if($result == 2) {
				$response['error'] = true;
				$response['message'] = $result;
			}
			else if($result == 0) {
				setResponseMessage($result);            
			}
		} else {
			setResponseMessage(false);
		}
	}
	else if($jsonarray["ProcessName"] == "ChangePassword") {
		$result = $db->ChangePassword($jsonarray);
		if($result) {
			setResponseMessage($result);
		}
		else {
			setResponseMessage($result);
		}
	}
	else if($jsonarray["ProcessName"] == "Logout") {
		$result = $db->updLoginFlg($jsonarray['userId'],0);
		if($result) {
			setResponseMessage($result);
		}
		else {
			setResponseMessage($result);
		}
	}
} else {
	setResponseMessage(false);
}

function setResponseMessage($result) {
	global $response;
	if($result === true || $result === 0) {
		$response['error'] = false;
		$response['message'] = 0;
	}
	else if($result === false || $result === 1) {
		$response['error'] = true;
		$response['message'] = 1;
	}
}

echo json_encode($response);

?>