<?php require_once('inc/session.php'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once('inc/meta.php'); ?>
        <title>Home :: Seahorse</title>

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
                <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
            </div> <!-- /.nav-wrapper -->
        </nav>

        <!-- Login Modal -->
        <div id="login" class="modal dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col s12">
                        <h3 class="light">Login</h3>
                        <form id="form-login" action="/login.php" method="post" validate>
                            <input id="referrer" class="hide" type="text" name="referrer" value="<?php echo $_SERVER['REQUEST_URI']; ?>" readonly required>

                            <div class="input-field col s12">
                                <input type="text" pattern="^[0-9]{7}$" name="user_id" id="user_id" class="validate" autofocus required>
                                <label for="user_id" data-error="7-digit CSU ID">CSU ID</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12">
                                <input type="password" name="password" id="password" class="validate" required>
                                <label for="password">Password</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12">
                                <a href="mailto:seahorse@csuoh.io?subject=[Seahorse]+Forgot+Password" class="deep-purple-text text-lighten-1"><b>Forgot password?</b></a>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12 right-align">
                                <button class="btn-large  waves-effect waves-light deep-purple lighten-1" type="submit">Login</button>
                            </div> <!-- /.input-field -->
                        </form>
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.modal-content -->
        </div> <!-- /.modal -->

        <!-- Banner -->
        <div id="index-banner" class="parallax-container valign-wrapper">
            <div class="section no-pad-bot">
                <div class="container valign">
                    <div class="row center">
                        <h1 class="header center white-text text-accent-2">Seahorse</h1>
                        <h5 class="header col s12 light">A modern replacement to Starfish based on Google's Material Design</h5>
                    </div> <!-- /.row -->

                    <div class="row center">
                        <a href="#register" class="btn-large waves-effect waves-light deep-purple lighten-1">Get started</a>
                    </div> <!-- /.row -->
                </div> <!-- /.container -->
            </div> <!-- /.section -->

            <div class="parallax"><img src="img/blue_poly_banner.png"></div>
        </div> <!-- /.parallax-container -->

        <div class="container">
            <!-- Help -->
            <div id="help" class="section no-pad-bot scrollspy">
                <div class="row">
                    <div class="col s12 center">
                        <h3 class="light">Start getting help in...</h3>
                        <h4 id="department" class="light deep-purple-text text-lighten-1"><i class="material-icons">mood</i></h4>
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.section -->
            <div class="divider large"></div>

            <!-- About Section -->
            <div id="about" class="section scrollspy">
                <div class="row">
                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center deep-purple-text text-lighten-1"><i class="material-icons">flash_on</i></h2>
                            <h5 class="center">Faster access to help</h5>

                            <p class="light">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a lacinia leo. Maecenas volutpat, mi non bibendum gravida, risus purus maximus lorem, vehicula consequat nulla enim id nisi. Nulla egestas volutpat sem, quis venenatis ex vestibulum scelerisque.</p>
                        </div> <!-- icon-block -->
                    </div> <!-- /.col -->

                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center deep-purple-text text-lighten-1"><i class="material-icons">group</i></h2>
                            <h5 class="center">User experience focused</h5>

                            <p class="light">Donec pharetra sapien ac sapien hendrerit ornare. Aliquam semper orci at cursus consectetur. Sed rhoncus ipsum ut porttitor bibendum. Nunc viverra eleifend odio non condimentum.</p>
                        </div> <!-- icon-block -->
                    </div> <!-- /.col -->

                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center deep-purple-text text-lighten-1"><i class="material-icons">settings</i></h2>
                            <h5 class="center">Easy to use</h5>

                            <p class="light">Cras quis condimentum mi, in tincidunt dui. Nulla facilisi. Vivamus a luctus nisl. Integer bibendum sodales eros, eu maximus dui imperdiet vel. Donec facilisis fringilla nibh ut sagittis. Donec venenatis pulvinar ipsum vel sagittis. Phasellus consequat et nisl sed lacinia.</p>
                        </div> <!-- icon-block -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.section -->
            <div class="divider"></div>

            <!-- Register Section -->
            <div id="register" class="section scrollspy">
                <div class="row">
                    <div class="col s12">
                        <h3 class="light center">Start your <b><span id="days">2147483647</span> day free</b> trail now.</h3>
                        <h5 class="light center">Then only <span class="deep-purple-text text-lighten-1">$0 per year</span>.</h5>
                    </div> <!-- /.col -->
                </div> <!-- /.row -->

                <div class="row card-panel">
                    <div class="col s12">
                        <form id="form-register" action="/register.php" method="post" validate>
                            <div class="input-field col m6 s12">
                                <input type="text" name="first_name" id="first_name" class="validate" required>
                                <label for="first_name">First Name</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col m6 s12">
                                <input type="text" name="last_name" id="last_name" class="validate" required>
                                <label for="last_name">Last Name</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12">
                                <input type="text" pattern="^[0-9]{7}$" name="user_id" id="user_id" class="validate" required>
                                <label for="user_id" data-error="7-digit CSU ID">CSU ID</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12">
                                <input type="email" pattern="^.*(csuohio\.edu)$" name="email" id="email" class="validate" required>
                                <label for="email" data-error="CSU email address">Email</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col m6 s12">
                                <input type="password" name="password" id="password" class="validate" required>
                                <label for="password">Password</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col m6 s12">
                                <input type="password" name="password_confirm" id="password_confirm" class="validate" required>
                                <label for="password_confirm">Confirm Password</label>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12">
                                <a href="#login" class="modal-trigger deep-purple-text text-lighten-1"><b>Already have an account?</b></a>
                            </div> <!-- /.input-field -->

                            <div class="input-field col s12 right-align">
                                <button class="btn-large deep-purple lighten-1" type="submit">Register</button>
                            </div> <!-- /.input-field -->
                        </form>
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.section -->
        </div> <!-- /.container -->

        <!-- Footer -->
        <footer class="page-footer deep-purple lighten-1">
            <div class="container">
                <div class="row">
                    <!-- Team -->
                    <div class="col l4 s12">
                        <h5 class="white-text">Team</h5>
                        <ul class="grey-text text-lighten-4">
                            <li><b><a href="https://github.com/IAreKyleW00t" class="white-text">Kyle Colantonio</a></b> - Frontend (HTML, CSS, JS)</li>
                            <li><b><a href="https://github.com/jbarto" class="white-text">Jeff Barto</a></b> - Backend (PHP, JS)</li>
                            <li><b><a href="#" class="white-text">Michael Artman</a></b> - Database (MariaDB/MySQL)</li>
                            <li><a href="https://github.com/IAreKyleW00t/seahorse" class="white-text">Source</a></li>
                        </ul>
                    </div> <!-- /.col -->

                    <!-- Credits -->
                    <div class="col l4 s12">
                        <h5 class="white-text">Credits</h5>
                        <ul class="grey-text text-lighten-4">
                            <li><a href="https://sso.csuohio.edu/" class="white-text">Starfish</a></li>
                            <li><a href="https://material.google.com/" class="white-text">Google Material Design</a></li>
                            <li><a href="http://materializecss.com/" class="white-text">MaterializeCSS</a></li>
                            <li><a href="https://jquery.com/" class="white-text">jQuery</a></li>
                        </ul>
                    </div> <!-- /.col -->

                    <!-- Contact -->
                    <div class="col l4 s12">
                        <h5 class="white-text">Contact</h5>
                        <ul class="grey-text text-lighten-4">
                            <li>Cleveland State University</li>
                            <li>2121 Euclid Avenue, Cleveland, OH 44115</li>
                            <li>(216) 687-2000</li>
                            <li><a href="mailto:seahorse@csuoh.io" class="white-text">seahorse@csuoh.io</a></li>
                        </ul>
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.container -->

            <div class="footer-copyright">
                <div class="container">
                    Copyright 2016, <a href="http://www.csuohio.edu/" class="white-text">Cleveland State University</a>
                </div>
            </div> <!-- /.footer-copyright -->
        </footer>

        <?php include_once('inc/footer.php'); ?>
        <?php include_once('inc/notice.php'); ?>
        <!-- Index JS -->
        <script>
            var days = Math.floor(Math.random() * (2147483647 - 1073741823 + 1)) + 1073741823;
            var departments = [];

            $(document).ready(function () {
                $('.parallax').parallax(); // Enable parallax
                $('.scrollspy').scrollSpy(); // Enable scrolling
                $('#days').text(days); // Random numberx

                /* Asynchronously load information from JSON file. */
                loadJSON('data/departments.json', function(data) {
                    json = JSON.parse(data); // Save JSON as Array

                    /* Pick a random department based on the `days` variable,
                        then randomly change it every 2 seconds. */
                    $('#department').text(json[days % json.length]);
                    setInterval(function() {
                        var rand = Math.floor((Math.random() * json.length) + 1);
                        $('#department').text(json[rand]);
                    }, 2000);
                });
            });
        </script>
    </body>
</html>