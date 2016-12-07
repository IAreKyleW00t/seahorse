<?php
    require_once('inc/session.php');
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
	if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_PERMISSION_LEVEL'] < 2) {
        echo json_encode($response);
        exit;
	}

    /* Check if request method is POST. */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        /* Check if all expected POST data was provided. */
        if (!isset($_POST['course'])) {
            echo json_encode($response);
            exit;
        }
        
        /* Format the course to be parsed later. */
        $course = preg_replace('/([A-Z]{3})([\d]{3})/', '$1 $2', $_POST['course']);

		/* Validate and save the course. */
        if (!preg_match('/^[A-Z]{3}[\s]+[0-9]{3}$/', $course)) {
            echo json_encode($response);
            exit;
        }
        $course = preg_split('/[\s]+/', $course);
        $department = $course[0];
        $course_number = $course[1];

        /* Check to make sure this course exists. */
		$query = $sql->prepare('SELECT id AS course_id from courses WHERE department = ? AND course_number = ? LIMIT 1');
        $query->execute(array(
            $department,
            $course_number
        ));

        if ($query->rowCount() != 1) {
            echo json_encode($response);
            exit;
        }

        /* Save the data from the database. */
        $row = $query->fetch(PDO::FETCH_ASSOC); // Entire row
        $course_id = $row['course_id'];

        /* Link this course to the current user. */
		$query = $sql->prepare('INSERT INTO account_courses (user_id, course_id) VALUES (?, ?)');
		$query->execute(array(
			$_SESSION['USER_ID'],
			$course_id
		));

        /* Set our response to be successful and include the course ID. */
        $response['success'] = true;
        $response['course'] = $course_id;
    }

    echo json_encode($response);
    exit;
?>
