<?php
// Start the session.
session_start();

// If the user is already logged in, send them to the home page.
if (isset($_SESSION['current_user'])) {
  $host = $_SERVER['HTTP_HOST'];
  $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $extra = '';
  header("location: http://$host$uri/$extra");
}

require_once('config.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">

    <!-- Title and description -->
    <title><?php echo $_TITLE_; ?></title>
    <meta name="description" content="<?php echo $_DESCRIPTION_; ?>">

    <!-- Grab our header font -->
    <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <!-- Grab Bootstrap -->
    <link href='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet' type='text/css'>

  </head>
  <body>
    <!-- Header -->
    <image src="http://<?PHP echo ($_SERVER['HTTP_HOST'] . $_IWP_DIR_); ?>/assets/images/HCI-Clippr-logo_dark.png" style="display: block; margin-top: 50px; margin-bottom: 10px; margin-left: auto; margin-right: auto;"/>

    <!-- Login error alert -->
    <?php if (isset($_SESSION['login_error'])): ?>
      <div class="alert alert-danger col-sm-4 col-sm-offset-4" >
      <p><?php print($_SESSION['login_error']['message']); ?></p>
      </div>
    <?php endif; ?>

    <!-- Logout success alert -->
    <?php if (isset($_GET['logout_success'])): ?>
      <div class="alert alert-success col-sm-4 col-sm-offset-4" >
        <p><?php print('You have been logged out.'); ?></p>
      </div>
    <?php endif; ?>

    <!-- Create account modal -->
    <div id="create_account_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="modalLabel">Create account</h4>
          </div>
          <div class="modal-body">
            <form id="create-account-form" action="logic_login.php" method="post" class="form-horizontal" role="form">
              <div class="form-group">
                <div class="col-sm-12">
                  <div class="panel panel-default">
                    <div class="panel-body">
                      <input type="email" name="email" class="form-control" id="input_email_create" placeholder="Enter your email" required><br />
                      <input type="password" name="password" class="form-control" id="input_password_create" placeholder="Enter your password" required><br />
                      <input type="password" name="password" class="form-control" id="input_password_confirm" placeholder="Confirm your password" required><br />
                      <input type="text" name="fname" class="form-control" id="inputFname" placeholder="Enter your first name" required><br />
                      <input type="text" name="lname" class="form-control" id="inputLname" placeholder="Enter your last name" required><br />
                      <button type="submit" class="btn btn-default col-sm-8 col-sm-offset-2" name="create">Create Account</button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    </div>

    <!-- Login form -->
    <form action="logic_login.php" method="post" class="form-horizontal" role="form">
      <div class="form-group">
        <div class="col-sm-4 col-sm-offset-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">Login</h2>
            </div>
            <div class="panel-body">
              <input type="email" name="email" class="form-control" id="input_email" placeholder="Enter your email" required><br />
              <input type="password" name="password" class="form-control" id="input_password" placeholder="Enter your password" required><br />
              <button type="submit" class="btn btn-default col-sm-8 col-sm-offset-2" id="sign-in-submit" name="sign_in">Sign in</button>
              <button type="button" class="btn btn-default col-sm-8 col-sm-offset-2" id="create_account_open_modal_button" data-toggle="modal" data-target="#create_account_modal">Create new account</button>
              <br />
              <br />
              <br />
              <br />
              <a href="#" onClick="loginUser('rm@example.com', '123testing');">Login as Demo user</a><br />
              <a href="#" onClick="loginUser('sb@example.com', '123testing');">Login as Stephanie Brisendine</a><br />
              <a href="#" onClick="loginUser('jd@example.com', '123testing');">Login as Jonathan Darling</a><br />
              <a href="#" onClick="loginUser('od@example.com', '123testing');">Login as Omar Darwish</a><br />
              <a href="#" onClick="loginUser('je@example.com', '123testing');">Login as Justin Ehlert</a><br />
              <a href="#" onClick="loginUser('tl@example.com', '123testing');">Login as Twinkle Lam</a>
            </div>
          </div>
        </div>
      </div>
    </form>
  </body>

  <!-- Grab Jquery -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <!-- Grab Bootstrap -->
  <script src='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
  <!-- Grab custom js -->
  <script src='assets/js/login_page/login_page.js'></script>
</html>