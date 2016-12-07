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
	if ($_SERVER['REQUEST_METHOD'] != 'GET' || !isset($_GET['id']) || !isset($_GET['c'])) {
        $_SESSION['NOTICE'] = "Could not process request.<br>Please try again.";
        header("Location: /panel.php");
        exit;
    }
    
    /* Save availability ID. */
    $id = $_GET['id'];
    $course_id = $_GET['c'];
    
    /* Get the availability based on the given ID. */
    $query = $sql->prepare('SELECT user_id AS tutor, day, start_time, end_time FROM availability WHERE id = FROM_BASE64(?)');
    $query->execute (array(
        $id
    ));
    $row = $query->fetch(PDO::FETCH_ASSOC); // Save time slot
    $tutor = $row['tutor'];
    $day = $row['day'];
    $start_time = new DateTime($row['start_time']);
    $end_time = new DateTime($row['end_time']);
    
    /* Get all the slots already taken for the tutor within the past week. */
    $query = $sql->prepare('SELECT id, DATE_FORMAT(time, "%h:%i %p") as time FROM meetings WHERE date > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND day = ? AND tutor_id = ?');
    $query->execute(array(
        $day,
        $tutor
    ));
    $meetings = $query->fetchAll(PDO::FETCH_KEY_PAIR); // Save all taken times slots.
    
    $times = array();
    while ($start_time < $end_time) {
        $time = $start_time->format('h:i A');
        $start_time->modify('+30 minutes');
        if (in_array($time, $meetings)) continue;
        
        $times[] = array(
            'start' => $time, 
            'end' => $start_time->format('h:i A'));
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once('inc/meta.php'); ?>
        <title>Schedule :: Seahorse</title>
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
                <h2>Schedule</h2>
                <div class="col s12">
                    <div class="row card-panel">
                        <div class="col s12">
                            <p class="flow-text">Please select a time slot below.<br>Time slots that are already taken will not be shown.</p>
                            <table id="times" class="striped">
                                <thead>
                                    <tr>
                                        <th class="center" data-field="start">Start</th>
                                        <th class="center" data-field="end">End</th>
                                        <th class="center" data-field="end">Length</th>
                                        <th class="center">Schedule</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($times as $i => $t): ?>
                                    <tr id ="<?php echo $i; ?>">
                                        <?php
                                            echo '<td class="center">' . $t['start'] . '</td>';
                                            echo '<td class="center">' . $t['end'] . '</td>';
                                            echo '<td class="center">30 minutes</td>';
                                        ?>
                                        <td class="center"><button id="schedule" class="btn btn-icon waves-effect waves-light deep-purple lighten-1"><i class="material-icons">save</i></button></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container -->
        <?php include_once('inc/footer.php'); ?>
        <?php include_once('inc/notice.php'); ?>
        <script>
            $(document).ready(function() {
                $('#times tbody').on('click', '#schedule', function() {
                    var $tr = $(this).closest('tr');
                    var time = $($tr).children('td:first').text();
                    
                    $.post('/add_meeting.php', {'tutor' : '<?php echo $tutor; ?>', 'course_id' : '<?php echo $course_id; ?>', 'time' : time, 'day' : '<?php echo $day; ?>'})
                        .fail(function(data) { // Unknown error
                                Materialize.toast('<span>An unknown error occurred.<br>Please see the console for more information.</span>', 5000);
                        })
                        .done(function(json) { // Valid response
                            if (json['success'] == true) { // Success
                                Materialize.toast('<span>Successfully scheduled timeslot!<br>Redirecting to meeting page...</span>', 5000);
                                window.setTimeout(function() {
                                    window.location.href = "/meeting.php?id=" + json['meeting'];
                                }, 1500);
                            } else { // Failure
                                Materialize.toast('<span>Failed to schedule timeslot.<br>Please try again.</span>', 5000);
                            }
                        });
                });
            });
        </script>
    </body>
</html>