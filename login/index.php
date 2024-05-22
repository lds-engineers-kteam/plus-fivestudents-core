<?php
require_once(__DIR__ . "/../config.php");

if(isset($_POST['submit'])){
  $username = $_POST['username'];
  $password = $_POST['password'];
  $args = ['username'=>$username, 'password'=>$password];
  $return = authenticate_user_login($args);
  // echo "<pre>..........";
  // print_r($return);
  // echo "</pre>";

  $errormessage = '<div class="alert alert-danger">'.get_string("loginfailed",'form').'</div>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Fivestudents Admin</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="../vendors/feather/feather.css">
  <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="../css/vertical-layout-light/style.css">
  <link rel="stylesheet" href="../css/login.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="../images/logo-mini.png" />
</head>

<body class="loginpage">
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4"></div>
          <div class="col-lg-4">
            <div class="auth-form-light text-left login-form">
              <h1><?php echo get_string("login",'form'); ?></h1>
              <form class="pt-3" method="post">
                <?php
                  // echo $OUTPUT->notification();
                ?>
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" required name="username" placeholder="<?php echo get_string("usernameoremail",'form'); ?>" value="schooladmin">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg"  required name="password" placeholder="<?php echo get_string("password",'form'); ?>" value="P@ssw0rd">
                </div>
                <div class="mt-3">
                  <?php
                  echo $errormessage?$errormessage:'';
                  ?>
                  <button class="btn btn-block btn-success btn-lg font-weight-medium auth-form-btn" name="submit" type="submit"><?php echo get_string("login",'form'); ?></button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-4"></div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="../vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="../js/off-canvas.js"></script>
  <script src="../js/hoverable-collapse.js"></script>
  <script src="../js/template.js"></script>
  <script src="../js/settings.js"></script>
  <script src="../js/todolist.js"></script>
  <!-- endinject -->
</body>

</html>
