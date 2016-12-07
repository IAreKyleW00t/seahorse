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

	/* Get all of the departments that tutoring is offered in. */
	$query = $sql->prepare('SELECT department FROM courses GROUP BY department');
	$query->execute ();
    $departments = $query->fetchAll(PDO::FETCH_COLUMN); // Save departments
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once('inc/meta.php'); ?>
        <title>Availability :: Seahorse</title>
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
                    <?php if (!isset($_SESSION['USER_ID'])): ?>
                    <li><a href="#login" class="modal-trigger btn waves-effect waves-light deep-purple lighten-1">Login</a></li>
                    <?php else: ?>
                    <li><a href="/logout.php" class="btn waves-effect waves-light deep-purple lighten-1">Logout</a></li>
                    <?php endif; ?>
    			</ul>

    			<!-- Mobile Navigation -->
    			<ul id="nav-mobile" class="side-nav">
    				<li><a href="/panel.php">Panel</a></li>
                    <?php if (!isset($_SESSION['USER_ID'])): ?>
                    <li><a href="#login" class="modal-trigger btn waves-effect waves-light deep-purple lighten-1">Login</a></li>
                    <?php else: ?>
                    <li><a href="/logout.php" class="btn waves-effect waves-light deep-purple lighten-1">Logout</a></li>
                    <?php endif; ?>
    			</ul>
    			<a href="#" data-activates="nav-mobile" class="button-collapse"><i
    				class="material-icons">menu</i></a>
    		</div> <!-- /.nav-wrapper -->
    	</nav>

        <!-- Login Modal -->
        <div id="login" class="modal dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col s12">
                        <h3 class="light">Login</h3>
                        <form id="form-login" action="/login.php" method="post" validate>
                            <div class="input-field col s12">
                                <input type="text" pattern="^[0-9]{7}$" name="user_id" id="user_id" class="validate" autofocus required>
                                <label for="user_id" data-error="7-digit CSU ID">CSU ID</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12">
                                <input type="password" name="password" id="password" class="validate" required>
                                <label for="password">Password</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12">
                                <a href="#!" class="deep-purple-text text-lighten-1"><b>Forgot password?</b></a>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12 right-align">
                                <button class="btn-large  waves-effect waves-light deep-purple lighten-1" type="submit">Login</button>
                            </div> <!-- /.input-field -->
                        </form>
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.modal-content -->
        </div> <!-- /.modal -->

        <div class="container">
            <div class="row">
                <h2>Availability</h2>
                <div class="col s12">
                    <p class="flow-text">Please select a department and course number.</p>

                    <div class="input-field col s6 l3">
                        <select name="department" id="department" required>
                            <option value="" disabled selected>Select department</option>
                            <?php foreach ($departments as $d) echo '<option value="' . $d . '">' . $d . '</option>'; ?>
                        </select>
                        <label for="department">Department</label>
                    </div> <!-- /.input-field -->

                    <div class="input-field col s6 l3">
                        <select name="course" id="course" disabled required>
                            <option value="" disabled selected>Select course</option>
                        </select>
                        <label for="course">Course</label>
                    </div> <!-- /.input-field -->

					<div class="input-field col s12 l3">
						<button id="submit" class="btn waves-effect waves-light deep-purple lighten-1" disabled>Submit</button>
					</div> <!-- /.input-field -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->

			<div class="row">
                <div class="col s12">
                    <!-- Tutors -->
                    <ul id="tutors" class="collapsible hide" data-collapsible="accordion"></ul>
                </div> <!-- /.col -->
			</div> <!-- /.row -->
        </div> <!-- /.container -->
        <?php include_once('inc/footer.php'); ?>
        <?php include_once('inc/notice.php'); ?>
        <script>
            $(document).ready(function() {
                /* Read all the courses for the selected department. */
                $('#department').on('change', function(e) {
                    var department = this.value;

                    $('#course')
                        .find('option')
                        .remove()
                        .end()
                        .append('<option value="" disabled selected>Select course</option>')
                        .val('')
                        .prop('disabled', false);

                    $.post('/read_courses.php', {'department' : department})
                        .done(function(json) {
                            if (json['success'] == true) {
                                $.each(json['courses'], function(i, course) {
                                    $('#course').append($('<option>', {
                                        'value': i,
                                        'text': course
                                    }));
                                });

                                $('#course').material_select(); // Select
                            } else {
                                Materialize.toast('<span>Failed to load department data.<br>Please try again.</span>', 5000);
                            }
                        });
                });

				$('#course').on('change', function() {
					$('#submit').prop('disabled', false);
				});

				$('#submit').on('click', function() {
					var course_id = $('#course').val();
					$('#tutors').empty();

					$.post('/read_availability.php', {'course_id' : course_id})
						.done(function(json) {
							if (json['success'] == true) {
                                $('#tutors').removeClass('hide');
								$.each(json['tutors'], function(i, tutor) {
                                    addTutor(tutor);
								});
							} else {
								Materialize.toast('<span>Failed to load data.<br>Please try again.</span>', 5000);
							}
						});
				});
                
                function addTutor(tutor) {
                    var $tutors = $('#tutors');
                    var $li = $('<li>');
                    var $table = $('<table>').attr('class', 'striped');
                    $table.append(
                        $('<thead>').append(
                            $('<tr>')
                                .append('<th class="center" data-field="day">Day</th>')
                                .append('<th class="center" data-field="start">Start</th>')
                                .append('<th class="center" data-field="end">End</th>')
                                .append('<th class="center" data-field="schedule">Schedule</th>')
                        )
                    );
                    
                    var $body = $('<tbody>');
                    $.each(tutor['availability'], function(i, avail) {
                        $body.append(
                            $('<tr>')
                                .append('<td class="center">' + avail['day'] + '</td>')
                                .append('<td class="center">' + avail['start_time'] + '</td>')
                                .append('<td class="center">' + avail['end_time'] + '</td>')
                                .append('<td class="center"><a href="/schedule.php?id=' + avail['id'] + '&c=' + $('#course').val() + '" id="schedule" class="btn btn-icon waves-effect waves-light deep-purple lighten-1"><i class="material-icons">schedule</i></a></td>')
                        );
                    });
                    
                    $li.append(
                        $('<div>').attr('class', 'collapsible-header').append(
                            '<b>' + tutor['first_name'] + ' ' + tutor['last_name'] + '</b>'
                        )
                    );
                    
                    $li.append(
                        $('<div>').attr('class', 'collapsible-body').append(
                            $('<p>').append(
                                $('<div>').attr('class', 'row').append(
                                    $('<div>').attr('class', 'col m2 hide-on-small-only').append(
                                        $('<img>').attr('src', 'http://identicon.org/?t=' + tutor['id'] + '&s=256').attr('class', 'responsive-img')
                                    )
                                ).append(
                                    $('<div>').attr('class', 'col m10').append(
                                        '<b>Name:</b> ' + tutor['first_name'] + ' ' + tutor['last_name'] + '<br>\
                                         <b>Email:</b> <a href="mailto:' + tutor['email'] + '" class="purple-text text-lighten-1">' + tutor['email'] + '</a><br>\
                                         <b>Min time:</b> 15 minutes<br>\
                                         <b>Max time:</b> 60 minutes<br>\
                                         <b><span class="red-text">Students may only schedule a maximum of 2 hours of tutoring per week.</span></b>' 
                                    )
                                )
                            ).append('<div class="divider"></div>')
                             .append('<h4>Availability</h4>')
                             .append($table.append($body))
                        )
                    );
                    
                    $tutors.append($li);
                }
            });
        </script>
    </body>
</html>