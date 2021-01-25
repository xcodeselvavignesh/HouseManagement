<?php
//creating reponse array
$response = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// including the Mail Status file
	require_once '../includes/MailStatus.php';
	//getting values
	$jsonarray = json_decode(file_get_contents('php://input'), true);
	$db = new MailStatus();
	if ($jsonarray["ProcessName"] == "mailStatusIndex") {
		$mailstatusData[] = $db->getMailStatusList();
		if (!empty($mailstatusData[0])) {
			foreach ($mailstatusData as $item) {
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
