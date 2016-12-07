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
	if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_PERMISSION_LEVEL'] < 1) {
        echo json_encode($response);
        exit;
	}

    /* Check if request method is POST. */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        /* Check if all expected POST data was provided. */
        if (!isset($_POST['department'])) {
            echo json_encode($response);
            exit;
        }

        /* Save department name. */
        $department = $_POST['department'];

        /* Select all of the courses for the specified department. If the
            department is invalid then no courses would be returned. */
		$query = $sql->prepare('SELECT TO_BASE64(id) AS id, course_number AS course FROM courses WHERE department = ? AND enabled = 1 ORDER BY course_number');
        $query->execute(array(
            $department
        ));

        /* Save the data from the database. */
        $rows = $query->fetchAll(PDO::FETCH_KEY_PAIR); // Entire row
        $response['courses'] = $rows;

        /* Set our response to be successful. */
        $response['success'] = true;
    }

    echo json_encode($response);
    exit;
?>
