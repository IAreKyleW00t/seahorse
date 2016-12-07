<?php
    require_once('inc/session.php');
    $sql = include('inc/sql_connection.php');

	/* Check if user is logged in and has permission.
		If not, redirect them to the index page. */
	if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_PERMISSION_LEVEL'] < 2) {
        $_SESSION['NOTICE'] = "You do not have permission to access that page.";
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
		if (!isset($_POST['days']) || !isset($_POST['start_time']) || !isset($_POST['end_time'])) {
			$_SESSION['NOTICE'] = "Could not process request.<br>Please try again.";
			header("Location: $referrer");
			exit;
		}

		/* Validate and save the days. */
        $days = $_POST['days'];
        foreach ($days as $day) {
            if (!preg_match('/^(MONDAY|TUESDAY|WEDNESDAY|THURSDAY|FRIDAY)$/', $day)) {
    			$_SESSION['NOTICE'] = "Invalid day provided.<br>Please try again.";
    			header("Location: $referrer");
    			exit;
            }
        }

		/* Validate and save the start and end time. */
		$start_time = date('H:i:s', strtotime($_POST['start_time']));
		$end_time = date('H:i:s', strtotime($_POST['end_time']));
		if (!preg_match('/^[\d]+(:[\d]+)*\s*(am|pm|AM|PM)$/', $_POST['start_time']) || !preg_match('/^[\d]+(:[\d]+)*\s*(am|pm|AM|PM)$/', $_POST['end_time'])) {
			$_SESSION['NOTICE'] = "Invalid timestamp provided.<br>Please try again.";
			header("Location: $referrer");
			exit;
		}

        /* Add a time slot for the user for each day they specified. */
        foreach ($days as $day) {
    		$query = $sql->prepare('INSERT INTO availability (user_id, day, start_time, end_time) VALUES (?, ?, ?, ?)');
    		$query->execute(array(
    			$_SESSION['USER_ID'],
                $day,
                $start_time,
                $end_time
    		));
        }

        /* Notify the user that their availability was updated successfully. */
        $_SESSION['NOTICE'] = "Account availability has been updated successfully!";
        header("Location: $referrer");
        exit;
    } else { // Ignore GET requests
        $_SESSION['NOTICE'] = "Unsupported operation!<br><code>GET</code> requests are not allowed on that page.";
        header('Location: /');
        exit;
    }
?>
