<?PHP
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if($_SERVER['REQUEST_METHOD']=="POST"){

	require __DIR__ . '/mail/Exception.php';
	require __DIR__ . '/mail/PHPMailer.php';
	require __DIR__ . '/mail/SMTP.php';
	require_once(__DIR__ . '/stripe/init.php');


	$mail = new PHPMailer;
	$mail->isSMTP(); 
	$mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
	$mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
	$mail->Port = 587; // TLS only
	$mail->SMTPSecure = 'tls'; // ssl is deprecated
	$mail->SMTPAuth = true;
	$mail->Username = 'your email address'; // email
	$mail->Password = 'app_specific_password'; // password
	$mail->setFrom('your email', 'your display name'); // From email and name

	// Create connection
	$conn = mysqli_connect("localhost", "username", "password", "databasename");
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	};

	mysqli_set_charset($conn, "utf8mb4");

	// Stripe Payment PHP:

	// Set your secret key. Remember to switch to your live secret key in production!
	// See your keys here: https://dashboard.stripe.com/account/apikeys
	\Stripe\Stripe::setApiKey('stripe_secret_key');

	// My Code:

	if ($_POST["formname"] == "login") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			while($loginRow = mysqli_fetch_assoc($res)) {
				if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = "INSERT INTO `log` (`username`, `datetime`) VALUES ('".$_POST["username"]."','".$_POST["datetime"]."')";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
					print $loginRow["Privileges"];
				} else {
					echo "Incorrect";
				};
			};
		} else {
			echo "Incorrect";
		};
	};
	if ($_POST["formname"] == "mainReady") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = "SELECT username FROM `accounts`";
					$res = mysqli_query($conn, $sql);

					$json_array = array();

					while($mainRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $mainRow;
					};
					echo json_encode($json_array);
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "membersReady") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = "SELECT username,Privileges,email FROM `accounts`";
					$res = mysqli_query($conn, $sql);

					$json_array = array();

					while($membersRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $membersRow;
					};
					echo json_encode($json_array);
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "addMember") {
		/**
		* In this case, we want to increase the default cost for BCRYPT to 12.
		* Note that we also switched to BCRYPT, which will always be 60 characters.
		*/
		$options = [
			'cost' => 12,
		];
		$password = password_hash($_POST["newpassword"], PASSWORD_BCRYPT, $options);
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = 'SELECT username FROM `accounts` WHERE username = "' . $_POST["newusername"] . '"';
					$res = mysqli_query($conn, $sql);
					
					if (mysqli_num_rows($res) > 0) {
						die("Already Exists");
					}

					$sql = 'SELECT username FROM `accounts` WHERE email = "' . $_POST["email"] . '"';
					$res = mysqli_query($conn, $sql);
					
					if (mysqli_num_rows($res) > 0) {
						die("Already Exists");
					}

					$mail->addAddress($_POST["email"]); // to email and name
					$mail->Subject = 'New Account';
					$mail->msgHTML('<h1>Hello '.$_POST["newusername"].',</h1><p>An account was just created for you by an administrator for:</p><p href="https://lucas-testing.000webhostapp.com/release/">The City of Truro Mariners Management Console</p><p>Username: '.$_POST["newusername"].'</p><p>Password: '.$_POST["newpasswordplain"].'</p><p>If you believe this was a mistake please contact us at: TruroMariners@gmail.com</p>'); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
					// $mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
					// $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file
					$mail->SMTPOptions = array(
						'ssl' => array(
							'verify_peer' => false,
							'verify_peer_name' => false,
							'allow_self_signed' => true
						)
					);
					if(!$mail->send()){
						echo "Mailer Error";
					}else{
						$sql = "INSERT INTO `accounts` (`username`,`password`,`email`,`Privileges`) VALUES ('".$_POST["newusername"]."','".$password."','".$_POST["email"]."','".$_POST["usertype"]."')";
						if (!mysqli_query($conn, $sql)) {
							die("Query Error");
						};
						echo "Success";
					}
			
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "deleteMember") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = "DELETE FROM `accounts` WHERE `username` = ('".$_POST["deleteusername"]."')";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "editMember") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
				$sql = 'SELECT username FROM `accounts` WHERE email = "' . $_POST["editemail"] . '"';
				$res = mysqli_query($conn, $sql);
				
				if (mysqli_num_rows($res) > 0) {
					$checkaccountresult = mysqli_fetch_assoc($res);
					if ($checkaccountresult["username"] != $_POST["editusername"]){
						die("Already Exists");
					}
				}
				
				$sql = "UPDATE accounts SET `email` = '".$_POST["editemail"]."',`Privileges` = '".$_POST["editprivileges"]."' WHERE `username` = '".$_POST["editusername"]."'";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "eventsReady") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = "SELECT `ID`, DATE_FORMAT(`datetime`, '%Y-%m-%d %H:%i') AS datetime, `submitted_by`, `title`, `description`, `location`, `accepted` FROM events;";
					$res = mysqli_query($conn, $sql);

					$json_array = array();

					while($eventsRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $eventsRow;
					};
					echo json_encode($json_array);
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "addEvent") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){

					if ($loginRow["Privileges"] == "0" && $_POST["accepted"] == "1") {
						echo "Unauthorised";
						return;
					}

					$sql = 'INSERT INTO `events` (`submitted_by`,`title`,`description`,`datetime`,`location`,`accepted`) VALUES ("'. $_POST["username"] .'","'. $_POST["title"] .'","'. $_POST["description"] .'","'. $_POST["datetime"] .'","'. $_POST["location"] .'","'. $_POST["accepted"] .'")';
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
					if ($loginRow["Privileges"] == "0"){
						echo "Submitted";
					}
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		}
	};
	if ($_POST["formname"] == "deleteEvent") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = "DELETE FROM events WHERE ID = ('".$_POST["ID"]."')";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "editEvent") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = 'UPDATE events SET title = "'.$_POST["title"].'", description = "'.$_POST["description"].'", datetime = "'.$_POST["datetime"].'", location = "'.$_POST["location"].'", accepted = "'.$_POST["accepted"].'" WHERE ID = "'.$_POST["ID"].'"';
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "resetPassword") {
		$sql = 'SELECT username FROM accounts WHERE email = "'.$_POST["email"].'"';
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			while($resetRow = mysqli_fetch_assoc($res)) {
				$resetusername = $resetRow["username"];
			};

			$sql = 'SELECT username FROM resetpass WHERE username = "'.$resetusername.'"';
			$res = mysqli_query($conn, $sql);

			if (mysqli_num_rows($res) > 0) {
				$sql = 'DELETE FROM resetpass WHERE username = "'.$resetusername.'"';
				if (!mysqli_query($conn, $sql)) {
					die("Query Error");
				};
			}

			code:

			$resetcode = rand(1000,9999);

			$sql = 'SELECT code FROM resetpass WHERE code = "'.$resetcode.'"';
			$res = mysqli_query($conn, $sql);

			if (mysqli_num_rows($res) > 0) {
				goto code;
			}

			$mail->addAddress($_POST["email"]); // to email and name
			$mail->Subject = 'Password Reset';
			$mail->msgHTML('<h1>Hello ' . $resetusername . ',</h1><p>A Password Reset Was Requested For This Account At:</p><p>'.$_POST["datetime"].'</p><p> To Confirm This Was You Please Paste This 4 Digit Code Into The Application:</p><h2>'.$resetcode.'</h2><p>If you believe this was a mistake please contact us at: TruroMariners@gmail.com</p>'); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
			// $mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
			// $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			if(!$mail->send()){
				echo "Mailer Error";
			}else{
				$sql = 'INSERT INTO `resetpass` (`username`,`code`,`datetime`) VALUES ("'.$resetusername.'","'.$resetcode.'","'.date("Y-m-d H:i:s").'")';
				if (!mysqli_query($conn, $sql)) {
					die("Query Error");
				};
		
				echo "Success";
			}
		} else {
			echo "NoAccount";
		};
	};
	if ($_POST["formname"] == "resetPasswordConfirmed") {
		/**
		* In this case, we want to increase the default cost for BCRYPT to 12.
		* Note that we also switched to BCRYPT, which will always be 60 characters.
		*/
		$options = [
			'cost' => 12,
		];
		$password = password_hash($_POST["newpass"], PASSWORD_BCRYPT, $options);
		$sql = 'SELECT username FROM resetpass WHERE code = "'.$_POST["code"].'"';
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			while($resetRow = mysqli_fetch_assoc($res)) {
				$resetusername = $resetRow["username"];
			};
			$sql = 'UPDATE accounts SET password = "'.$password.'" WHERE username = "'.$resetusername.'"';
			if (!mysqli_query($conn, $sql)) {
				die("Query Error");
			};

			echo "Success";
		} else {
			echo "Incorrect";
		}
	};
	if ($_POST["formname"] == "messagesReady") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = 'SELECT username FROM `accounts` WHERE username != "'.$_POST["username"].'"';
					$res = mysqli_query($conn, $sql);

					$json_array = array();

					while($messagesRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $messagesRow;
					};
					echo json_encode($json_array);
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "messagesLoad") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
				if ($_POST["ID"] == 0){$sql = "SELECT * FROM ( SELECT * FROM `messages` WHERE (`messageFrom`, `messageTo`) in ( ( '".$_POST["username"]."', '".$_POST["contact"]."' ),( '".$_POST["contact"]."', '".$_POST["username"]."' ) ) AND `ID` > ".$_POST["ID"]." ORDER BY `datetime` DESC LIMIT 50 ) Var1 ORDER BY `datetime` ASC";}
				else {$sql = "SELECT * FROM ( SELECT * FROM `messages` WHERE (`messageFrom`, `messageTo`) in ( ( '".$_POST["username"]."', '".$_POST["contact"]."' ),( '".$_POST["contact"]."', '".$_POST["username"]."' ) ) AND `ID` < ".$_POST["ID"]." ORDER BY `datetime` DESC LIMIT 50 ) Var1 ORDER BY `datetime` ASC";}
				$res = mysqli_query($conn, $sql);

				$json_array = array();

				while($messagesRow = mysqli_fetch_assoc($res)) {
					//$messagesRow["message"] = json_decode($messagesRow["message"]);
					$json_array[] = $messagesRow;
				};
				echo json_encode($json_array);
			} else {
				echo "Unauthorised";
			}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "messageSend") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
				$sql = 'INSERT INTO `messages` (`messageFrom`,`messageTo`,`message`,`file`,`datetime`) VALUES ("'.$_POST["username"].'","'.$_POST["contact"].'","'.$_POST["message"].'",'.$_POST["file"].',"'.$_POST["datetime"].'")';
				if (!mysqli_query($conn, $sql)) {
					die("Query Error");
				};
				echo "Success";
			} else {
				echo "Unauthorised";
			}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "message-read") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = 'UPDATE messages set messageRead = 1 WHERE ID = '.$_POST["ID"];
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "messagesLoadNotify") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
				$sql = "SELECT * FROM `messages` WHERE `messageTo` = '".$_POST["username"]."' AND `messageRead` = 0";
				$res = mysqli_query($conn, $sql);


				$json_array = array();

				while($messagesRow = mysqli_fetch_assoc($res)) {
					$json_array[] = $messagesRow;
				};
				echo json_encode($json_array);
			} else {
				echo "Unauthorised";
			}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "membership-payment") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
				$intent = \Stripe\PaymentIntent::create([
					'amount' => 1300,
					'currency' => 'gbp',
					// Verify your integration in this guide by including this parameter
					'metadata' => ['integration_check' => 'accept_a_payment'],
				]);
				echo $intent["client_secret"];
			} else {
				echo "Unauthorised";
			}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "membership-payment-success") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
				$sql = 'INSERT INTO `payments` (`id`,`date`,`username`,`type`,`amount`) VALUES ("'.$_POST["ID"].'","'.$_POST["datetime"].'","'.$_POST["username"].'","Membership Payment","£13.00")';
				if (!mysqli_query($conn, $sql)) {
					die("Query Error");
				};
				echo "Success";
			} else {
				echo "Unauthorised";
			}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "payments") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = 'SELECT * FROM `payments` WHERE (`username`,`type`) = ("'.$_POST["username"].'","Membership Payment") ORDER BY `date` DESC LIMIT 1';
					$res = mysqli_query($conn, $sql);


					$json_array = array();

					while($paymentsRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $paymentsRow;
					};
					echo json_encode($json_array);
			} else {
				echo "Unauthorised";
			}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "request-access") {
		$sql = "SELECT email FROM `accounts` WHERE email = ('" . $_POST["email"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			echo "Already Access";
			return;
		};
		$mail->addAddress('truromariners@gmail.com', 'Truro Mariners'); // to email and name
		$mail->Subject = 'Access Request';
		$mail->msgHTML('<h1>'.$_POST["email"].' has requested access.</h1>'); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
		// $mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
		// $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		if(!$mail->send()){
			echo "Mailer Error";
		}else{
			echo "Request Confirmed";
		}
	};
	if ($_POST["formname"] == "addOutgoing") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = 'INSERT INTO `expenditure` (`item`,`description`,`datetime`,`location`,`member`) VALUES ("'.$_POST["item"].'","'.$_POST["description"].'","'.$_POST["datetime"].'","'.$_POST["location"].'","'.$_POST["member"].'")';
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					}
					echo "Success";
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "usernamesAccounting") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = 'SELECT `username` FROM `accounts`';
					$res = mysqli_query($conn, $sql);


					$json_array = array();

					while($usernameRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $usernameRow;
					};
					echo json_encode($json_array);
			} else {
				echo "Unauthorised";
			}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "addPayment") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = 'INSERT INTO `payments` (`date`,`inPerson`,`type`,`username`,`amount`,`description`) VALUES ("'.$_POST["datetime"].'","'.$_POST["inperson"].'","'.$_POST["type"].'","'.$_POST["memberusername"].'","'.$_POST["amount"].'","'.$_POST["description"].'")';
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					}
					echo "Success";
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "loadExpenditure") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = "SELECT * FROM `expenditure`";
					$res = mysqli_query($conn, $sql);

					$json_array = array();

					while($expenditureRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $expenditureRow;
					};
					echo json_encode($json_array);
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "editExpend") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = 'UPDATE `expenditure` SET item = "'.$_POST["item"].'", description = "'.$_POST["description"].'", datetime = "'.$_POST["datetime"].'", location = "'.$_POST["location"].'", member = "'.$_POST["member"].'" WHERE ID = "'.$_POST["ID"].'"';
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "deleteExpend") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = "DELETE FROM `expenditure` WHERE ID = ('".$_POST["ID"]."')";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "loadPayments") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = "SELECT * FROM `payments`";
					$res = mysqli_query($conn, $sql);

					$json_array = array();

					while($paymentsRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $paymentsRow;
					};
					echo json_encode($json_array);
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "editPayment") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = 'UPDATE `payments` SET `description` = "'.$_POST["description"].'", date = "'.$_POST["datetime"].'", inPerson = "'.$_POST["inPerson"].'", username = "'.$_POST["username"].'", amount = "'.$_POST["amount"].'" WHERE id = "'.$_POST["ID"].'"';
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "deletePayment") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			$refund = \Stripe\Refund::create([
				'payment_intent' => $_POST["ID"],
			]);
			if ($refund["status"] == "succeeded"){
				if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = "DELETE FROM `payments` WHERE id = ('".$_POST["ID"]."')";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
				} else {
					echo "Unauthorised";
				}
			} else {
				echo "Failed";
			}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "attendEvent") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			$loginRow = mysqli_fetch_assoc($res);
			$sql = "SELECT id FROM `event_attending` WHERE (`id`,`event_title`,`username`) = ('". $_POST['id'] ."','". $_POST['title'] ."','". $_POST['username'] ."')";
			$res = mysqli_query($conn, $sql);

			if (mysqli_num_rows($res) > 0) {
				die("Already Attending");
			};
			
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = 'INSERT INTO `event_attending` (`id`,`event_title`,`username`) VALUES ("'. $_POST["id"] .'","'. $_POST["title"] .'","'. $_POST["username"] .'")';
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
					echo "Success";
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		}
	};
	if ($_POST["formname"] == "eventsAttendingReady") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = "SELECT * FROM event_attending WHERE username = '".$_POST["username"]."'";
					$res = mysqli_query($conn, $sql);

					$json_array = array();

					while($eventsRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $eventsRow;
					};
					echo json_encode($json_array);
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "loadAllAttending") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True && $loginRow["Privileges"] == "1"){
					$sql = "SELECT * FROM event_attending";
					$res = mysqli_query($conn, $sql);

					$json_array = array();

					while($eventsRow = mysqli_fetch_assoc($res)) {
						$json_array[] = $eventsRow;
					};
					echo json_encode($json_array);
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "unattendEvent") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = "DELETE FROM event_attending WHERE id = ('".$_POST["id"]."')";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
					echo "Success";
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		}
	};
	if ($_POST["formname"] == "changeUsername") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = "UPDATE accounts SET `username` = '".$_POST["updateUsername"]."' WHERE `username` = '".$_POST["username"]."'";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
					echo "Success";
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "changePassword") {
				/**
		* In this case, we want to increase the default cost for BCRYPT to 12.
		* Note that we also switched to BCRYPT, which will always be 60 characters.
		*/
		$options = [
			'cost' => 12,
		];
		$password = password_hash($_POST["updatePassword"], PASSWORD_BCRYPT, $options);
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = "UPDATE accounts SET `password` = '".$password."' WHERE `username` = '".$_POST["username"]."'";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
					echo "Success";
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};
	if ($_POST["formname"] == "changeEmail") {
		$sql = "SELECT username,password,Privileges FROM `accounts` WHERE (`accounts`.`username`) = ('" . $_POST["username"] . "')";
		$res = mysqli_query($conn, $sql);

		if (mysqli_num_rows($res) > 0) {
			// output data of each row
			$loginRow = mysqli_fetch_assoc($res);
			if (password_verify($_POST["password"], $loginRow["password"]) == True){
					$sql = "UPDATE accounts SET `email` = '".$_POST["updateEmail"]."' WHERE `username` = '".$_POST["username"]."'";
					if (!mysqli_query($conn, $sql)) {
						die("Query Error");
					};
					echo "Success";
				} else {
					echo "Unauthorised";
				}
		} else {
			echo "Unauthorised";
		};
	};

	// close the result.
	mysqli_free_result($res);
	mysqli_close($conn);
	return;
}

?>