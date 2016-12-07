<?php
    require_once('inc/session.php');
	require_once('inc/sendmail.php');
	$sql = include('inc/sql_connection.php');

    /* Set the Content-Type to be in JSON format. */
    header('Content-Type: application/json');

    /* Create our response array which will
        be "unsuccessful" by default. */
    $response = array(
        'success' => false
    );

	/* Check if user is logged in and has permission.
		If not, return unsuccessful. */
	if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_PERMISSION_LEVEL'] < 1) {
        echo json_encode($response);
        exit;
	}

    /* Check if request method is POST. */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        /* Check if all expected POST data was provided. */
        if (!isset($_POST['tutor']) || !isset($_POST['course_id']) || !isset($_POST['time']) || !isset($_POST['day'])) {
            echo json_encode($response);
            exit;
        }
        
        $tutor = $_POST['tutor'];
        $course_id = $_POST['course_id'];
        $time = date('H:i:s', strtotime($_POST['time']));
        $day = $_POST['day'];

        /* Insert the new meeting into the database. */
		$query = $sql->prepare('INSERT INTO meetings (student_id, tutor_id, course_id, date, time, day) VALUES (?, ?, FROM_BASE64(?), CURDATE(), ?, ?)');
        $query->execute(array(
            $_SESSION['USER_ID'],
            $tutor,
            $course_id,
            $time,
            $day
        ));
        $meeting_id = base64_encode($sql->lastInsertId());

        /* Get the tutors email so we can notify them about the new meeting. */
        $query = $sql->prepare('SELECT first_name, email from accounts WHERE id = ?');
        $query->execute(array(
            $tutor
        ));
        $tutor = $query->fetch(PDO::FETCH_ASSOC);

		/* Send a confirmation email to the user. */
		$from = "no-reply@csuoh.io";
		$subject = "Seahorse: Meeting Reminder";
		$message = $tutor['first_name'] . ",<br><br>"
				 . "You have a meeting scheduled for $day at $time.<br><br>"
				 . "For more information please visit your <a href=\"https://seahorse.csuoh.io/panel.php\">https://seahorse.csuoh.io/panel.php</a>";
		sendmail($tutor['email'], $subject, $message, $from, "no-reply");

        /* Set our response to be successful and include the course ID. */
        $response['success'] = true;
        $response['meeting'] = $meeting_id;
    }

    echo json_encode($response);
    exit;
?>
