<?php
include('config.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register & Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="style2.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .links {
      display: flex !important;
      justify-content: space-around !important;
      padding: 0 4rem !important;
      margin-top: 1.2rem !important;
      font-weight: bold !important;
    }

    button {
      color: #084466 !important;
      border: none !important;
      background-color: transparent !important;
      font-size: 1rem !important;
      font-weight: bold !important;
      margin-bottom: 1rem !important;
    }

    button:hover {
      text-decoration: underline !important;
    }
  </style>
</head>

<body>
  <div class="container" id="signUp" style="display: none;">
    <h1 class="form-title">Register</h1>
    <form method="post" action="">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="fname" id="fname" placeholder="First Name" required>
        <label for="fname">First Name</label>
      </div>
      <div class="input-group">
        <i class="fas fa-user" aria-hidden="true"></i>
        <input type="text" name="lname" id="lname" placeholder="Last Name" required>
        <label for="lname">Last Name</label>
      </div>
      <div class="input-group">
        <i class="fas fa-user" aria-hidden="true"></i>
        <input type="text" name="username" id="username" placeholder="Username" required>
        <label for="username">Username</label>
      </div>
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <label for="email">Email</label>
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <label for="password">Password</label>
      </div>
      <input type="submit" class="btn" value="Sign Up" name="signUp">
      <p class="or">
        ---------------or---------------
      </p>
      <!--<div class="icons">-->
      <!--  <i class="fab fa-google"></i>-->
      <!--  <i class="fab fa-facebook"></i>-->
      <!--</div>-->
      <div class="links">
        <p>Already Have An Account?</p>
        <button id="signInButton">Sign In</button>
      </div>
    </form>
  </div>

  <div class="container" id="signIn">
    <h1 class="form-title">Sign In</h1>
    <form method="post" action="">
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <label for="email">Email</label>
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <label for="password">Password</label>
      </div>
      <p class="recover">
        <a href="#">Recover Password</a>
      </p>
      <input type="submit" class="btn" value="Sign In" name="signIn">
      <p class="or">
        ---------------or---------------
      </p>
      <!--<div class="icons">-->
      <!--  <i class="fab fa-google"></i>-->
      <!--  <i class="fab fa-facebook"></i>-->
      <!--</div>-->
      <div class="links">
        <p>Don't have an account?</p>
        <button id="signUpButton">Register</button>
      </div>
    </form>
  </div>
  <script src="script2.js"></script>
</body>
<?php
if (isset($_POST['signUp'])) {
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  $username_check_sql = "SELECT * FROM `userinfo` WHERE `username` = '$username'";
  $username_result = $conn->query($username_check_sql);

  $email_check_sql = "SELECT * FROM `userinfo` WHERE `email` = '$email'";
  $email_result = $conn->query($email_check_sql);

  if ($username_result->num_rows > 0) {
    $_SESSION['alert'] = "<script>
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Username already exists'
              });
            </script>";
    echo '<meta http-equiv="refresh" content="0;url=login.php">';
    exit();
  } elseif ($email_result->num_rows > 0) {
    $_SESSION['alert'] = "<script>
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Email already exists'
              });
            </script>";
    echo '<meta http-equiv="refresh" content="0;url=login.php">';
    exit();
  } else {
    $sql = "INSERT INTO `userinfo`(`fname`, `lname`, `username`, `email`, `password`) VALUES ('$fname', '$lname', '$username', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
      $_SESSION['alert'] = "<script>
                Swal.fire({
                  icon: 'success',
                  title: 'Success',
                  text: 'New account created successfully',
                  allowOutsideClick: false
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = 'login.php';
                  }
                });
              </script>";
      echo '<meta http-equiv="refresh" content="0;url=login.php">';
      exit();
    } else {
      $_SESSION['alert'] = "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Error: " . $sql . "<br>" . $conn->error . "'
                });
              </script>";
      echo '<meta http-equiv="refresh" content="0;url=login.php">';
      exit();
    }
  }

  $conn->close();
}

?>
<?php

if (isset($_POST['signIn'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Query to check username and password
  $sql = "SELECT userid, usertype FROM userinfo WHERE email = '$email' AND password = '$password'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $usertype = $row['usertype'];
    $userid = $row['userid'];
    $_SESSION['userid'] = $userid;
    $_SESSION['usertype'] = $usertype;
    switch ($usertype) {
      case 'Admin':
        $_SESSION['alert'] = "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: 'Welcome Admin',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/admin/';
                        }
                    });
                </script>";
        echo '<meta http-equiv="refresh" content="0;url=login.php">';
        exit();
        break;
      case 'Incharge':
        $_SESSION['alert'] = "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: 'Welcome Incharge',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/incharge/';
                        }
                    });
                </script>";
        echo '<meta http-equiv="refresh" content="0;url=login.php">';
        exit();
        break;
      case 'Client':
        $_SESSION['alert'] = "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: 'Welcome Client',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/index.php';
                        }
                    });
                </script>";
        echo '<meta http-equiv="refresh" content="0;url=login.php">';
        exit();
        break;
      default:
        $_SESSION['alert'] = "<script>
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'error',
                                                    text: 'Unknown usertype',
                                                    allowOutsideClick: false
                                                })
                                            </script>";
        echo '<meta http-equiv="refresh" content="0;url=login.php">';
        exit();
    }
  } else {
    $_SESSION['alert'] = "<script>
        Swal.fire({
            icon: 'info',
            title: 'Invalid',
            text: 'Sign In Failed, Incorrect Email or Password',
        })
    </script>";
    echo '<meta http-equiv="refresh" content="0;url=login.php">';
    exit();
  }

  $conn->close();
}
?>

<?php
if (isset($_SESSION['alert'])) {
  echo $_SESSION['alert'];
  unset($_SESSION['alert']);
}
?>

</html>