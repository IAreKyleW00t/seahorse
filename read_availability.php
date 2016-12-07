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
        if (!isset($_POST['course_id'])) {
            echo json_encode($response);
            exit;
        }

        /* Save course ID number. */
        $course_id = $_POST['course_id'];

        
        /* Check to make sure this course exists. */
		$query = $sql->prepare('SELECT a.id, a.first_name, a.last_name, a.email FROM account_courses ac JOIN accounts a ON a.id = ac.user_id WHERE course_id = FROM_BASE64(?) GROUP BY a.id');
        $query->execute(array(
            $course_id
        ));

        /* Save the data from the database. */
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); // Entire row
        
        /* Get each tutors availability and add it to each row. */
        foreach ($rows as $i => $row) {
            $query = $sql->prepare('SELECT TO_BASE64(id) AS id, day, DATE_FORMAT(start_time, "%h:%i %p") AS start_time, DATE_FORMAT(end_time, "%h:%i %p") AS end_time FROM availability WHERE user_id = ? ORDER BY day');
            $query->execute(array(
                $row['id']
            ));
            
            /* Add the availability to each row. */
            $rows[$i]['availability'] = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        
        /* Add all the tutor information to the response. */
        $response['tutors'] = $rows;

        /* Set our response to be successful. */
        $response['success'] = true;
    }

    echo json_encode($response);
    exit;
?>
