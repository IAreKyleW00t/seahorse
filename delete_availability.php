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
		if (!isset($_POST['id'])) {
            echo json_encode($response);
            exit;
		}

		/* Save the ID. */
        $id = $_POST['id'];

        /* Delete the timeslot the user has selected. */
		$query = $sql->prepare('DELETE FROM availability WHERE id = ? AND user_id = ?');
		$query->execute(array(
            $id,
			$_SESSION['USER_ID']
		));

        /* Set our response to be successful. */
        $response['success'] = true;
        $response['id'] = $id;
    }

    echo json_encode($response);
    exit;
?>
