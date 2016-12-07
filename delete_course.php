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
        
        /* Delete the matching course that is linked to the user.
            If the course doesn't exist then nothing will happen. */
		$query = $sql->prepare('DELETE FROM account_courses WHERE course_id = (SELECT id FROM courses c WHERE department = ? AND course_number = ?) AND user_id = ?');
        $query->execute(array(
            $department,
            $course_number,
            $_SESSION['USER_ID']
        ));
        
        /* Set our response to be successful. */
        $response['success'] = true;
    }
    
    echo json_encode($response);
    exit;
?>