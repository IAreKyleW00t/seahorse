<?php
	require_once('inc/session.php');
	$sql = include('inc/sql_connection.php');

	/* Check if user is logged in and has permission.
		If not, redirect them to the index page. */
	if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_PERMISSION_LEVEL'] < 1) {
        $_SESSION['NOTICE'] = "You do not have permission to access that page.";
		header('Location: /');
		exit;
	}

	/* Check if request method is not GET. */
	if ($_SERVER['REQUEST_METHOD'] != 'GET' || !isset($_GET['id'])) {
        $_SESSION['NOTICE'] = "Could not process request.<br>Please try again.";
        header("Location: /panel.php");
        exit;
    }
    
    /* Save availability ID. */
    $id = $_GET['id'];
    
    /* Get all the slots already taken for the tutor within the past week. */
    $query = $sql->prepare('SELECT CONCAT(a.first_name, " ", a.last_name) AS tutor, a.email, m.date, DATE_FORMAT(m.time, "%h:%i %p") AS time, CONCAT(c.department, " ", c.course_number) AS course FROM meetings m JOIN accounts a ON (a.id = m.tutor_id) JOIN courses c ON (c.id = m.course_id) WHERE m.id = FROM_BASE64(?) AND m.student_id = ?');
    $query->execute(array(
        $id,
        $_SESSION['USER_ID']
    ));

    /* Verify the current user is the creator of the meeting. */
    if ($query->rowCount() != 1) {
        $_SESSION['NOTICE'] = "You do not have permission to access that page.";
        header("Location: /panel.php");
        exit;
    }
        
    $meeting = $query->fetch(PDO::FETCH_ASSOC); // Save meeting information
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once('inc/meta.php'); ?>
        <title>Meeting :: Seahorse</title>
        <?php include_once('inc/header.php'); ?>
    </head>
    <body>
		<!-- Navigation -->
		<nav class="white" role="navigation">
			<div class="nav-wrapper container">
				<!-- Logo -->
				<a href="/" id="logo-container" class="brand-logo">Seahorse</a>

				<!-- Desktop Navigation -->
				<ul id="nav-desktop" class="right hide-on-med-and-down">
					<li><a href="/panel.php">Panel</a></li>
					<li><a href="/logout.php" class="btn waves-effect waves-light deep-purple lighten-1">Logout</a></li>
				</ul>

				<!-- Mobile Navigation -->
				<ul id="nav-mobile" class="side-nav">
					<li><a href="/panel.php">Panel</a></li>
					<li><a href="/logout.php" class="btn waves-effect waves-light deep-purple lighten-1">Logout</a></li>
				</ul>
				<a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
			</div> <!-- /.nav-wrapper -->
		</nav>

        <div class="container">
            <div class="row">
                <h2>Meeting</h2>
                <div class="col s12">
                    <div class="row card-panel">
                        <div class="col s12">
                            <p class="flow-text">Below is information relating to your tutoring session.<br>Please email your tutor if you cannot make it to your meeting.</p>
                            <p class="flow-text">
                                <b>Class:</b> <?php echo $meeting['course']; ?><br>
                                <b>Tutor:</b> <?php echo $meeting['tutor']; ?> (<a href="mailto:<?php echo $meeting['email'];?>" class="purple-text text-lighten-1"><?php echo $meeting['email']; ?></a>)<br>
                                <b>Date:</b> <?php echo $meeting['date']; ?><br>
                                <b>Time:</b> <?php echo $meeting['time']; ?>
                            </p>
                        </div> <!-- /.col -->
                    </div> <!-- /.row -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container -->
        <?php include_once('inc/footer.php'); ?>
        <?php include_once('inc/notice.php'); ?>
    </body>
</html>