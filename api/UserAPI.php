<?php
//creating reponse array
$response = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// including the Mail File
	require_once '../includes/Common.php';
	// including the User Add file
	require_once '../includes/User.php';
	// including the Mail File
	require_once '../includes/SendingMail.php';
	// Mail Value
	$mailContentId = "MAIL0001";
	//getting values
	$jsonarray = json_decode(file_get_contents('php://input'), true);
	//Instantiating User Class
	$db = new User();
	if($jsonarray["ProcessName"] == "Register") {
		$getExistsMailId = $db->getExistsMailId($jsonarray['EmailId']);
		if ($getExistsMailId) {
			$dbCommon = new Common();
			$mailContent = $dbCommon->getMailContent($mailContentId);
			$userType = 2;
			$maxUserId = $dbCommon->getMaxUserId($userType);
			if (count($maxUserId) == 0) {
				$userId = "HMS0001";
			} else {
				$suffix = substr($maxUserId['userId'],-4);
				$Addsuffix = intval($suffix)+1;
				$newsuffix = str_pad($Addsuffix, 4, 0 , STR_PAD_LEFT);
				$userId = "HMS".$newsuffix;
			}
			$header = $mailContent['header']." ".$jsonarray['SurName'].",\n";
			$content = $mailContent['content'];
			$content = str_replace('LLLLL', $userId, $content);
			$content = str_replace('PPPPP', $jsonarray['Password'], $content);
			$content = str_replace('MMMMM', $jsonarray['MobileNo'], $content);
			$content = $content."\n";
			$footer = "Thanks & Regards"."\n"."Click Here To Visit Microbit.com Team";
			$bodyrep = $header.$content.$footer;
			// Search Function
			if ($db->addUser($userId,$jsonarray,$mailContent,$bodyrep)) {
				$dbMAil = new SendingMail();
				if ($dbMAil->sendIntimationMail($jsonarray['EmailId'],$mailContent['subject'],$bodyrep)) {
					setResponseMessage(true);
				} else {
					setResponseMessage(false);
				}
			} else {
				setResponseMessage(false);
			}
		} else {
			$response['error'] = true;
			$response['message'] = 2;
		}
	} 
	else if ($jsonarray["ProcessName"] == "userIndex") {
		$userData[] = $db->getUserList($jsonarray);
		if (!empty($userData[0])) {
			foreach ($userData as $item) {
				while ($row = $item->fetch_assoc()) {
					array_push($response, $row);
				}
			}
			setResponseMessage(true);
		} else {
			setResponseMessage(false);
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