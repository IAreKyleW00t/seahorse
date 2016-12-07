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

    /* Parse the String representation of the
        users permission level. */
    $type = null;
    switch ($_SESSION['USER_PERMISSION_LEVEL']) {
        case 1: // Student
            $type = 'Student';
            break;
        case 2: // Tutor
            $type = 'Tutor';
            break;
        default: // Admin
            $type = 'Administrator';
            break;
    }

    /* If the user is a tutor, then load all the courses
        they can help in and their availability. */
    $course = null; $times = null; $tutor_meetings = null;
    if ($_SESSION['USER_PERMISSION_LEVEL'] >= 2) {
        /* Get all the courses the user has added to
            their account. */
        $query = $sql->prepare('SELECT CONCAT(c.department, " ", c.course_number) AS course FROM courses c LEFT JOIN account_courses ac ON (ac.course_id = c.id) WHERE c.enabled = 1 AND ac.user_id = ? ORDER BY course');
        $query->execute (array(
            $_SESSION['USER_ID']
        ));
        $courses = $query->fetchAll(PDO::FETCH_COLUMN); // Save courses

        /* Get all the times the user has added to
            their account. */
        $query = $sql->prepare('SELECT id, day, DATE_FORMAT(start_time, "%h:%i %p") AS start_time, DATE_FORMAT(end_time, "%h:%i %p") AS end_time FROM availability WHERE user_id = ? ORDER BY day');
        $query->execute (array(
            $_SESSION['USER_ID']
        ));
        $times = $query->fetchAll(PDO::FETCH_ASSOC); // Save times

        /* Get all of the meetings that are scheduled with the tutor. */
        $query = $sql->prepare('SELECT TO_BASE64(m.id) AS id, m.date, DATE_FORMAT(m.time, "%h:%i %p") AS time, CONCAT(c.department, " ", c.course_number) AS course FROM meetings m JOIN courses c ON (c.id = m.course_id) WHERE m.date > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND m.tutor_id = ? ORDER BY m.date');
        $query->execute(array(
            $_SESSION['USER_ID']
        ));
        $tutor_meetings = $query->fetchAll(PDO::FETCH_ASSOC); // Save all meetings
    }

    /* Get all the meetings the student has scheduled. */
    $query = $sql->prepare('SELECT TO_BASE64(m.id) AS id, m.date, DATE_FORMAT(m.time, "%h:%i %p") AS time, CONCAT(c.department, " ", c.course_number) AS course FROM meetings m JOIN courses c ON (c.id = m.course_id) WHERE m.date > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND m.student_id = ? ORDER BY m.date');
    $query->execute(array(
        $_SESSION['USER_ID']
    ));
    $meetings = $query->fetchAll(PDO::FETCH_ASSOC); // Save all meetings
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	    <?php include_once('inc/meta.php'); ?>
	    <title>Panel :: Seahorse</title>
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
					<li class="active"><a href="/panel.php">Panel</a></li>
					<li><a href="/logout.php" class="btn waves-effect waves-light deep-purple lighten-1">Logout</a></li>
				</ul>

				<!-- Mobile Navigation -->
				<ul id="nav-mobile" class="side-nav">
					<li class="active"><a href="/panel.php">Panel</a></li>
					<li><a href="/logout.php" class="btn waves-effect waves-light deep-purple lighten-1">Logout</a></li>
				</ul>
				<a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
			</div> <!-- /.nav-wrapper -->
		</nav>

		<div class="container">
            <div class="row">
                <h2>Panel</h2>
                <div class="col s12 m6 l4">
                    <div class="col s12 center">
                        <img src="http://identicon.org/?t=<?php echo $_SESSION['USER_ID']; ?>&s=256" class="responsive-img">
                    </div> <!-- /.col -->

                    <div class="col s12">
                        <div class="card-panel">
                            <h5>Hi <?php echo htmlspecialchars($_SESSION['USER_FIRST_NAME']); ?>!</h5>
                            <p>
                                Welcome to your personal user panel. You can adjust your settings and schedule appointments.
                                If you're a tutor then you can also adjust who and when you tutor.<br><br>
                                <b>CSU ID:</b> <?php echo $_SESSION['USER_ID']; ?><br>
                                <b>Account Type:</b> <?php echo $type; ?>
                            </p>
                        </div> <!-- /.card-panel -->
                    </div> <!-- /.col -->

                    <div class="col s12 center">
                        <a href="/availability.php" class="btn-large deep-purple lighten-1">Schedule Appointment</a>
                    </div>
                </div> <!-- /.col -->

                <div class="col s12 m6 l8">
                    <?php if (count($meetings) == 0): ?>
                    <p class="center flow-text">You have no upcoming meetings.</p>
                    <?php else: ?>
                    <div class="card-panel">
                        <div class="row">
                            <div class="col s12">
                            <h4>Meetings</h4>
                            <p class="flow-text">You have <b><?php echo count($meetings); ?></b> upcoming meetings. Click one for more information.</p>

                            <table id="meetings" class="striped">
                                <thead>
                                    <tr>
                                        <th class="center" data-field="date">Date</th>
                                        <th class="center" data-field="start">Start</th>
                                        <th class="center" data-field="course">Course</th>
                                        <th class="center"></th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($meetings as $m): ?>
                                    <tr id="<?php echo $m['id']; ?>">
                                        <?php
                                            echo '<td class="center">' . $m['date'] . '</td>';
                                            echo '<td class="center">' . $m['time'] . '</td>';
                                            echo '<td class="center">' . $m['course'] . '</td>';
                                        ?>
                                        <td class="center"><a href="/meeting.php?id=<?php echo $m['id']; ?>" id="view" class="btn btn-icon waves-effect waves-light deep-purple lighten-1"><i class="material-icons">forward</i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($_SESSION['USER_PERMISSION_LEVEL'] < 2): ?>
                    <?php elseif (count($tutor_meetings) == 0): ?>
                    <p class="center flow-text">You have no upcoming sessions.</p>
                    <?php else: ?>
                    <div class="card-panel">
                        <div class="row">
                            <div class="col s12">
                            <h4>Sessions</h4>
                            <p class="flow-text">You have <b><?php echo count($tutor_meetings); ?></b> upcoming sessions.</p>

                            <table id="meetings" class="striped">
                                <thead>
                                    <tr>
                                        <th class="center" data-field="date">Date</th>
                                        <th class="center" data-field="start">Start</th>
                                        <th class="center" data-field="course">Course</th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($tutor_meetings as $m): ?>
                                    <tr id="<?php echo $m['id']; ?>">
                                        <?php
                                            echo '<td class="center">' . $m['date'] . '</td>';
                                            echo '<td class="center">' . $m['time'] . '</td>';
                                            echo '<td class="center">' . $m['course'] . '</td>';
                                        ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($_SESSION['USER_PERMISSION_LEVEL'] >= 2): ?>
                    <div class="card-panel">
                        <div class="row no-margin-bottom">
                            <div id="courses" class="col s12">
                                <h4>Courses</h4>
                                <h5 class="light">Enter all of the courses that you are able to tutor in.</h5>
                                <br>

                                <div class="chips chips-initial"></div>
                            </div> <!-- /.col -->
                        </div> <!-- /.row -->

                        <div class="row">
                            <div class="col s12">
                                <h4>Availability</h4>
                                <h5 class="light">Enter all the times you are available to tutor during the school week.</h5>
                                <br>

                                <?php if (count($times) != 0): ?>
                                <table id="times" class="striped">
                                    <thead>
                                        <tr>
                                            <th class="center" data-field="day">Day</th>
                                            <th class="center" data-field="start">Start</th>
                                            <th class="center" data-field="end">End</th>
                                            <th class="center"></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($times as $t): ?>
                                        <tr id ="<?php echo $t['id']; ?>">
                                            <?php
                                                echo '<td class="center">' . $t['day'] . '</td>';
                                                echo '<td class="center">' . $t['start_time'] . '</td>';
                                                echo '<td class="center">' . $t['end_time'] . '</td>';
                                            ?>
                                            <td class="center"><button id="delete" class="btn btn-icon waves-effect waves-light deep-purple lighten-1"><i class="material-icons">delete</i></button></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <br>
                                <?php endif; ?>

                                <form id="form-add-availability" action="/add_availability.php" method="post" validate>
                                    <input id="referrer" class="hide" type="text" name="referrer" value="<?php echo $_SERVER['REQUEST_URI']; ?>" readonly required>

                                    <div class="input-field col s12 l6">
                                        <select name="days[]" id="days" multiple="multiple" required>
                                            <option value="" disabled selected>Select day(s)</option>
                                            <option value="MONDAY">Monday</option>
                                            <option value="TUESDAY">Tuesday</option>
                                            <option value="WEDNESDAY">Wednesday</option>
                                            <option value="THURSDAY">Thursday</option>
                                            <option value="FRIDAY">Friday</option>
                                        </select>
                                        <label for="days[]">Day(s)</label>
                                    </div> <!-- /.input-field -->

                                    <div class="input-field col s6 l3">
                                        <input type="text" name="start_time" id="start_time" class="validate" pattern="^[\d]+(:[\d]+)*\s*(am|pm|AM|PM)$" required>
                                        <label for="start_time" data-error="Format: HH:MM (AM|PM)">Start time</label>
                                    </div> <!-- /.input-field -->

                                    <div class="input-field col s6 l3">
                                        <input type="text" name="end_time" id="end_time" class="validate" pattern="^[\d]+(:[\d]+)*\s*(am|pm|AM|PM)$" required>
                                        <label for="end_time" data-error="Format: HH:MM (AM|PM)">End time</label>
                                    </div> <!-- /.input-field -->

                                    <div class="input-field col s12">
                                        <button id="add-availability" class="btn waves-effect waves-light deep-purple lighten-1 right">Add availability</button>
                                    </div> <!-- /.input-field -->
                                </form>
                            </div> <!-- /.col -->
                        </div> <!-- /.row -->
                    </div> <!-- /.card-panel -->
                    <?php endif; ?>
                </div> <!-- /.col -->
            </div> <!-- /.row -->
		</div> <!-- /.container -->

        <?php include_once('inc/footer.php'); ?>
        <?php include_once('inc/notice.php'); ?>
        <?php if ($_SESSION['USER_PERMISSION_LEVEL'] >= 2): ?>
        <!-- Panel JS (Tutor) -->
        <script>
            $(document).ready(function() {
                /* Add all of the classes to the chips list. Unfortunately
                    due to how the data needs to be formatted we must loop
                    through each element one at a time. */
                $('.chips-initial').material_chip({
                    data: [
                        <?php foreach ($courses as $course) echo "{'tag': '$course'},"; ?>
                    ],
                });

                /* Create an event handler that will link a given course to
                    the current user when a new chip is added. */
                $('.chips').on('chip.add', function(e, chip) {
                    var course = chip.tag.toUpperCase(); // Force uppercase
                    $.post('/add_course.php', {'course' : course})
                        .fail(function(data) { // Unknown error
                                Materialize.toast('<span>An unknown error occurred.<br>Please see the console for more information.</span>', 5000);
                                $('.chip').last().remove();
                        })
                        .done(function(json) { // Valid response
                            /* Check if the action was successful. */
                            if (json['success'] == true) { // Success
                                Materialize.toast('<span>Successfully added course!</span>', 5000);
                            } else { // Failure
                                Materialize.toast('<span>Failed to add course.<br>Please try again.</span>', 5000);
                                $('.chip').last().remove();
                            }
                        });
                });

                /* Create an event handler that will unlink the cource from
                    the current user when a chip is deleted. */
                $('.chips').on('chip.delete', function(e, chip) {
                    var course = chip.tag.toUpperCase(); // Force uppercase
                    $.post('/delete_course.php', {'course' : course})
                        .fail(function(data) { // Unknown error
                                Materialize.toast('<span>An unknown error occurred.<br>Please see the console for more information.</span>', 5000);
                        })
                        .done(function(json) { // Valid response
                            /* Check if the action was successful. */
                            if (json['success'] == true) { // Success
                                Materialize.toast('<span>Successfully deleted course!</span>', 5000);
                            } else { // Failure
                                Materialize.toast('<span>Failed to deleted course.<br>Please try again.</span>', 5000);
                            }
                        });
                });

                /* Delete a time slot from the current users list of
                    availabilities when the specified delete button
                    is clicked. */
				$('#times tbody').on('click', '#delete', function() {
					var $tr = $(this).closest('tr');
					var id = $tr.attr('id'); // Time slot ID
                    $.post('/delete_availability.php', {'id' : id})
                        .fail(function(data) { // Unknown error
                                Materialize.toast('<span>An unknown error occurred.<br>Please see the console for more information.</span>', 5000);
                        })
                        .done(function(json) { // Valid response
                            if (json['success'] == true) { // Success
								$tr.remove();
                                Materialize.toast('<span>Successfully deleted timeslot!</span>', 5000);
                            } else { // Failure
                                Materialize.toast('<span>Failed to deleted timeslot.<br>Please try again.</span>', 5000);
                            }
                        });
				});
            });
        </script>
        <?php endif; ?>
    </body>
</html>