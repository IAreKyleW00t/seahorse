<?php
    require_once('inc/session.php');
    require_once('inc/update_tokens.php');
    $sql = include('inc/sql_connection.php');

	/* Check if request method is GET. */
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        /* Check if all expected GET data was provided. */
        if (!isset($_GET['token'])) {
            $_SESSION['NOTICE'] = "Could not process request.<br>Please try again.";
            header('Location: /');
            exit;
        }

		/* Save token. Validation not needed here. */
		$token = $_GET['token'];

        /* Check if this token exists. If not, then stop the
            ctication process. */
        $query = $sql->prepare('SELECT id AS token_id, user_id FROM tokens WHERE token = ? AND type = ? LIMIT 1');
        $query->execute(array(
            $token,
            'REGISTER'
        ));

        if ($query->rowCount() != 1) {
            $_SESSION['ERROR'] = "Invalid token provided.<br>Please try again.";
            header('Location: /');
            exit;
        }

        /* Save the data from the database. */
        $row = $query->fetch(PDO::FETCH_ASSOC); // Entire row
        $token_id = $row['token_id'];
        $user_id = $row['user_id'];

        /* Activate the account the token belongs to. */
        $query = $sql->prepare('UPDATE accounts SET permission_level = ? WHERE id = ?');
        $query->execute(array(
            1,
            $user_id
        ));

        /* Remove the now used token. */
        $query = $sql->prepare('DELETE FROM tokens WHERE id = ?');
        $query->execute(array(
            $token_id
        ));

        /* Notify the user that their account was activated successfully and
            redirect them to the index page. */
        $_SESSION['NOTICE'] = "Account activated successfully!<br>You may now login.";
        header('Location: /');
        exit;
    } else { // Ignore POST requests
        $_SESSION['NOTICE'] = "Unsupported operation!<br><code>POST</code> requests are not allowed on that page.";
        header('Location: /');
        exit;
    }
?>
