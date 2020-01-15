<?php
session_start();

if(isset($_SESSION['mailSend']))
{
  echo "Eine Anfrage wurde bereits eingereicht!";
  return;
}

if(isset($_POST['captcha']))
{
  $recaptcha = $_POST['captcha'];

  $object = new Recaptcha();
  $response = $object->verifyResponse($recaptcha);

  if(isset($response['success']) and $response['success'] != true)
  {
  	echo "An Error Occured and Error code is :".$response['error-codes'];
  }
  else
  {
    if(isset($_POST['req']) && isset($_POST['email']))
    {
      $data_email = $_POST['email'];

      if($data_email == "")
      {
          echo 'Bitte eine EMail-Adresse eingeben!';
          return;
      }

      if(strlen($data_email) > 200)
      {
          echo 'EMail-Adresse zu lang!';
          return;
      }

      if (!filter_var($data_email, FILTER_VALIDATE_EMAIL)) {
          echo "Bitte nur gültige EMail-Adresse eintragen!";
          return;
      }


      /*
      $to      = "dennisrauscherd@gmail.com";
      $subject = 'Azoron.de | MSG';
      $message = 'REQUEST BY: ' . $data_email;
      $headers = 'From: ' . $data_email . "\r\n" .
          'Reply-To: ' . $data_email . "\r\n" .
          'X-Mailer: PHP/' . phpversion();

      mail($to, $subject, $message, $headers);
      */
      $_SESSION['mailSend'] = true;
      echo 'success';
      return;
    }
  }
}




class Recaptcha{

	public function verifyResponse($recaptcha){

		$remoteIp = $this->getIPAddress();

		// Discard empty solution submissions
		if (empty($recaptcha)) {
			return array(
				'success' => false,
				'error-codes' => 'missing-input',
			);
		}

		$getResponse = $this->getHTTP(
			array(
				'secret' => "6LfcuBoUAAAAAOQq7Sz2Zyy6XtGEDMFHe_UFbEn_",
				'remoteip' => $remoteIp,
				'response' => $recaptcha,
			)
		);

		// get reCAPTCHA server response
		$responses = json_decode($getResponse, true);

		if (isset($responses['success']) and $responses['success'] == true) {
			$status = true;
		} else {
			$status = false;
			$error = (isset($responses['error-codes'])) ? $responses['error-codes']
				: 'invalid-input-response';
		}

		return array(
			'success' => $status,
			'error-codes' => (isset($error)) ? $error : null,
		);
	}


	private function getIPAddress(){
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
		 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	private function getHTTP($data){

		$url = 'https://www.google.com/recaptcha/api/siteverify?'.http_build_query($data);
		$response = file_get_contents($url);

		return $response;
	}
}
