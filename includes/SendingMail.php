<?php

include("Mail/class.phpmailer.php");
include("Mail/class.smtp.php");

class SendingMail {

	public static function sendIntimationMail($mailId,$subject,$body,$ccAddr=null,$bccAddr=null,$pdf_array = array(),$excel_array = array()){

		$mail             = new PHPMailer();
		$body             = $body;
		$mail->Mailer     = "smtp";
		$mail->IsSMTP();
		$mail->SMTPKeepAlive = true;
		$mail->SMTPDebug  = 0;
		$mail->CharSet    = 'utf-8';
		$mail->SMTPAuth   = true;				// enable SMTP authentication
		$mail->SMTPSecure = "tls";				// sets the prefix to the servier
		$mail->Host       = "v133-130-69-156.myvps.jp";	// set the SMTP server
		//$mail->Host     = "localhost";	    // set the SMTP server
		$mail->Port       = 587;				// set the SMTP port
		$mail->Username   = "staff@microbit.co.jp";	// MB MAIL username
		$mail->Password   = "U8LXOc]GMtS8";         // MB MAIL password
		$mail->From       = "staff@microbit.co.jp";
		$mail->FromName   = "Microbit Pvt. Ltd.,";
		$mail->AddReplyTo("staff@microbit.co.jp","Admin Microbit");
		$mail->Subject    =	$subject;
		$mail->AltBody    = $subject;
		$mail->WordWrap   = 50; // set word wrap

		$mail->MsgHTML(nl2br($body));

		$mail->AddAddress($mailId); 

		if($ccAddr != null){
			$mail->AddCC($ccAddr);
		}
		if($bccAddr != null){
			$mail->AddBCC($bccAddr);
		}

		$mail->IsHTML(true); // send as HTML

		// Send Multiple Pdf File Process Start.
		if(!empty($pdf_array)) {
			foreach ($pdf_array as $key => $value) {
				$mail->AddAttachment($value);
			}
		}
		// Send Multiple Pdf File Process End.

		// Send Multiple Excel File Process Start.
		if(!empty($excel_array)) {
			foreach ($excel_array as $key => $value) {
				$mail->AddAttachment($value);
			}
		}
	
		$done = $mail->Send();
		if($done) {
			return $done;
		} else {
			return false;
		}
	}
}

?>