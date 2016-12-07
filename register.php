	<?php
	require_once('inc/session.php');
	require_once('inc/sendmail.php');
	require_once('inc/update_tokens.php');
	$sql = include('inc/sql_connection.php');

	/* Check if user is logged in.
		If so, redirect them to the index page. */
	if (isset($_SESSION['USER_ID'])) {
		header('Location: /');
		exit;
	}

	/* Check if request method is POST. */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		/* Check if we have a referrer and default to the
			index page if it was not provided. */
		$referrer = '/';
		if (isset($_POST['referrer'])) {
			$referrer = $_POST['referrer'];
		}

		/* Check if all expected POST data was provided. */
		if (!isset($_POST['first_name']) || !isset($_POST['last_name']) || !isset($_POST['email']) || !isset($_POST['user_id']) || !isset($_POST['password']) || !isset($_POST['password_confirm'])) {
			$_SESSION['NOTICE'] = "Could not process request.<br>Please try again.";
			header("Location: $referrer");
			exit;
		}

		/* Validate and save the email. */
		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^.*(csuohio\.edu)$/', $email)) {
			$_SESSION['NOTICE'] = "Invalid email address.<br>Please try again.";
			header("Location: $referrer");
			exit;
		}

		/* Validate and save the CSU ID. */
		$flags = array(
			'options' => array(
				'min_range' => 0000000,
				'max_range' => 9999999
			)
		);
		$user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
		if (!filter_var($user_id, FILTER_VALIDATE_INT, $flags)) {
			$_SESSION['NOTICE'] = "Invalid CSU ID.<br>Please try again.";
			header("Location: $referrer");
			exit;
		}

		/* Check to make sure both passwords match. */
		if (strcmp($_POST['password'], $_POST['password_confirm']) != 0) {
			$_SESSION['NOTICE'] = "Passwords do not match.<br>Please try again.";
			header("Location: $referrer");
			exit;
		}

		/* Save first and last name. Validation not needed here. */
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];

		/* Hash the password using PHP's `password_hash` function.
			This will automatically use the "best" hashing algorithm
			based on `PASSWORD_DEFAULT`. For extra security, we rotate the
			password by 13 characters. */
		$hash = password_hash(str_rot13($_POST['password']), PASSWORD_DEFAULT);

		/* Check if an account with that CSU ID already exists. If so, stop the
			account creation process and notify the user. */
		$query = $sql->prepare('SELECT 1 FROM accounts WHERE id = ? LIMIT 1');
		$query->execute(array(
			$user_id
		));

		if ($query->rowCount() != 0) {
			$_SESSION['NOTICE'] = "An account with that CSU ID already exists.<br><a href=\"/contact.php\">Contact us</a> for help!";
			header("Location: $referrer");
			exit;
		}

		/* Save the account to the database after we have validated all the
			information provided by the user. */
		$query = $sql->prepare('INSERT INTO accounts (id, first_name, last_name, email, hash, creation_ip) VALUES (?, ?, ?, ?, ?, ?)');
		$query->execute(array(
			$user_id,
			$first_name,
			$last_name,
			$email,
			$hash,
			ip2long($_SERVER['REMOTE_ADDR'])
		));

		/* Generate a new one-time token to activate the users account. */
		$token = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
		$expires_on = date('Y-m-d H:i:s', strtotime('+30 minutes'));

		/* Save this token in the database to be used later. This token
			if linked to the account that created it and will automatically
			expire in 30 minutes. If the user needs a new token then they
			must attempt to login and new one will be sent. */
		$query = $sql->prepare('INSERT INTO tokens (user_id, token, type, expires_on) VALUES (?, ?, ?, ?)');
		$query->execute(array(
			$user_id,
			$token,
			'REGISTER',
			$expires_on
		));

		/* Send an activation email to the user. */
		$from = "no-reply@csuoh.io";
		$subject = "Seahorse: Account Activation";
		$message = "$first_name,<br><br>"
				 . "Thanks for signing up to use Seahorse! Click the link below to be finish activating your account.<br><br>"
				 . "Please note <b>this link will expire in 30 minutes!</b> If you need a new activation link, simply login and new one will be sent to you.<br><br>"
				 . "<a href=\"https://seahorse.csuoh.io/activate.php?token=$token\">https://seahorse.csuoh.io/activate.php?token=$token</a>";
		sendmail($email, $subject, $message, $from, "no-reply");

		/* Notify the user that their account was created successfully and
			redirect them back to the previous page. */
		$_SESSION['NOTICE'] = "An activation email was sent to $email.<br><b>Please be sure to check your Spam/Junk folder!</b>";
		header("Location: $referrer");
		exit;
    } else { // Ignore GET requests
        $_SESSION['NOTICE'] = "Unsupported operation!<br><code>GET</code> requests are not allowed on that page.";
        header("Location: /");
        exit;
    }
?>
