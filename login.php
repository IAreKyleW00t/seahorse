<?php
    require_once('inc/session.php');
    require_once('inc/sendmail.php');
    require_once('inc/update_tokens.php');
    $sql = include('inc/sql_connection.php');

	/* Check if user is logged in.
		If so, redirect them to the index page. */
	if (isset($_SESSION['USER_ID'])) {
        $_SESSION['NOTICE'] = "You are already logged in.";
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
        if (!isset($_POST['user_id']) || !isset($_POST['password'])) {
            $_SESSION['NOTICE'] = "Could not process request.<br>Please try again.";
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

        /* Check if an account with that CSU ID exists. If not, stop the
			login process and notify the user. */
        $query = $sql->prepare('SELECT id AS user_id, first_name, last_name, email, hash, permission_level FROM accounts WHERE id = ? LIMIT 1');
        $query->execute(array(
            $user_id
        ));

        if ($query->rowCount() != 1) {
            $_SESSION['NOTICE'] = "Invalid login credentials.<br>Please try again.";
            header("Location: $referrer");
            exit;
        }

        /* Save the data from the database. */
        $row = $query->fetch(PDO::FETCH_ASSOC); // Entire row
        $user_id = $row['user_id'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];
        $hash = $row['hash'];
        $permission_level = $row['permission_level'];

        /* Verify the users password using PHP's `password_verify` function. */
        if (!password_verify(str_rot13($_POST['password']), $hash)) {
            $_SESSION['NOTICE'] = "Invalid login credentials.<br>Please try again.";
            header("Location: $referrer");
            exit;
        }

        /* Check if the users password needs rehashed using PHP's
            `password_needs_rehash`. If so, automatically rehash the password
            with the new algorithm. */
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $new_hash = password_hash(str_rot13($_POST['password']), PASSWORD_DEFAULT);
            $query = $sql->prepare('UPDATE accounts SET hash = ? WHERE id = ?');
            $query->execute(array(
                $new_hash,
                $user_id
            ));
        }

        /* Check if the users account is activated or disabled. If the account
            is not activated then create a new registration token and send
            a new email. If the account is disabled then do not allow them
            to login. */
        if ($permission_level < 0) { // Disabled
            $_SESSION['NOTICE'] = "Your account has been disabled.<br>Please <a href=\"/contact.php\">contact us</a> to resolve this issue.";
            header("Location: $referrer");
            exit;
        } else if ($permission_level == 0) { // Not activated
    		/* Generate a new one-time token to activate the users account. */
    		$token = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
    		$expires_on = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    		/* Save this token in the database to be used later.
                See `register.php` for more inforation. */
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

    		/* Notify the user that a new activation email was sent and
    			redirect them back to the previous page. */
    		$_SESSION['NOTICE'] = "A new activation email was sent to $email.<br><b>Please be sure to check your Spam/Junk folder!</b>";
    		header("Location: $referrer");
    		exit;
        } else { // Normal
            /* Save account information into SESSION. */
            $_SESSION['USER_ID'] = $user_id;
            $_SESSION['USER_FIRST_NAME'] = $first_name;
            $_SESSION['USER_LAST_NAME'] = $last_name;
            $_SESSION['USER_FULL_NAME'] = $first_name . ' ' . $last_name;
            $_SESSION['USER_EMAIL'] = $email;
            $_SESSION['USER_PERMISSION_LEVEL'] = $permission_level;

           /* Notify the user that they were logged in successfully and
               redirect them back to the previous page. */
           $_SESSION['NOTICE'] = "Welcome, $first_name!";
           header("Location: /panel.php");
           exit;
        }
    } else { // Ignore GET requests
        $_SESSION['NOTICE'] = "Unsupported operation!<br><code>GET</code> requests are not allowed on that page.";
        header("Location: /");
        exit;
    }
?>
