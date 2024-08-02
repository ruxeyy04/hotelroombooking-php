<?php include('layouts/header.php'); ?>
<!-- Breadcrumb Section Begin -->
<div class="breadcrumb-section pb-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-text">
                    <h2>My Profile</h2>
                    <div class="bt-option">
                        <a href="index-2.html">Home</a>
                        <span>My Profile</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?=1?>
<!-- Breadcrumb Section End -->

<section class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <h4 class="text-center">Profile Image</h4>
                <div class="featured-dishes-img" style="background-image: url('/profile_img/<?= $userinfo['image'] == null ? 'default.jpg' : $userinfo['image'] ?>'); background-position: center;
                                background-size: cover;
                                height: 350px;
                                width: auto;
                                border-radius: 15px;
                                box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;">
                </div>
                <?php
                if (isset($_GET['update_img']) != 1) { ?>
                    <div class="d-flex align-items-center justify-content-center mt-lg-4">
                        <button type="button" class="btn btn-success" onclick="location.replace('profile.php?update_img')">Update Image</button>
                    </div>
                <?php } else { ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3 mt-3">
                            <input class="form-control" type="file" id="formFile" name="profile_img">
                        </div>
                        <div class="d-flex align-items-center justify-content-center mt-lg-4">
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger" onclick="location.replace('profile.php')">Cancel</button>
                                <button type="submit" class="btn btn-success" name="edit_pic">Update Image</button>
                            </div>
                        </div>
                    </form>
                <?php }
                ?>
            </div>
            <div class="col-lg-7 offset-lg-1">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="mb-3">Profile Information</h3>
                        <form action="#" class="contact-form" method="post">
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" placeholder="First Name" name="fname" value="<?= $userinfo['fname'] ?>" required>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" placeholder="Last Name" name="lname" value="<?= $userinfo['lname'] ?>" required>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" placeholder="Username" name="username" value="<?= $userinfo['username'] ?>" required>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" placeholder="Email" name="email" value="<?= $userinfo['email'] ?>" required>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" placeholder="Contact Info" name="contact" value="<?= $userinfo['contact'] ?>" required>
                                </div>
                                <div class="col-lg-6">
                                       <select name="gender"  class="form-select">
                                            <option selected disabled> - Select Gender - </option>
                                            <option value="Male" <?=$userinfo['gender'] == "Male" ? 'selected' : ''?>>Male</option>
                                            <option value="Female" <?=$userinfo['gender'] == "Female" ? 'selected' : ''?>>Female</option>
                                        </select>
                                </div>
                                <button type="submit" name="updateprofile">Save Changes</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <h3 class="mb-3 mt-5">Change Password</h3>
                        <form action="#" class="contact-form" method="post">
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="password" placeholder="Old Password" name="oldpass" required>
                                </div>
                                <div class="col-lg-12">
                                    <input type="password" placeholder="New Password" name="newpass" required>
                                </div>
                                <div class="col-lg-12">
                                    <input type="password" placeholder="Confirm Password" name="confirmpass" required>
                                    <button type="submit" name="updatepassword">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

if (isset($_POST['updateprofile'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['contact'];
    $gender = $_POST['gender'];

    $sql_profile = "UPDATE userinfo SET 
        fname = ?, 
        lname = ?, 
        contact = ?,
        gender = '$gender',
        email = ?, 
        username = ? 
        WHERE userid = ?";

    $userinfo = $conn->prepare($sql_profile);
    $userinfo->bind_param("sssssi", $fname, $lname, $phone, $email, $username, $userid);

    if ($userinfo->execute() === TRUE) {
        $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'success',
            title: 'Successfully Updated Profile',
        });
    </script>";
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit();
    } else {
        $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'errpr',
            title: '$conn->error',
        });
    </script>";
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit();
    }
    $userinfo->close();
    $conn->close();
}
?>
<?php
if (isset($_POST['updatepassword'])) {
    $oldpass = $_POST['oldpass'];
    $newpass = $_POST['newpass'];
    $confirmpass = $_POST['confirmpass'];

    $sql_get_password = "SELECT password FROM userinfo WHERE userid = ?";
    $stmt = $conn->prepare($sql_get_password);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($current_password);
    $stmt->fetch();

    if ($oldpass === $current_password) {
        if ($newpass === $confirmpass) {
            $sql_update_password = "UPDATE userinfo SET password = ? WHERE userid = ?";
            $update_stmt = $conn->prepare($sql_update_password);
            $update_stmt->bind_param("si", $newpass, $userid);

            if ($update_stmt->execute() === TRUE) {
                $_SESSION['alert'] = "<script>
                        Toast.fire({
                            icon: 'success',
                            title: 'Successfully Updated Password',
                        });
                    </script>";
                echo '<meta http-equiv="refresh" content="0;url=profile.php">';
                exit();
            } else {
                $_SESSION['alert'] = "<script>
                Toast.fire({
                    icon: 'error',
                    title: '{$conn->error}',
                });
            </script>";
                echo '<meta http-equiv="refresh" content="0;url=profile.php">';
                exit();
            }
            $update_stmt->close();
        } else {
            $_SESSION['alert'] = "<script>
                Toast.fire({
                    icon: 'error',
                    title: 'New password and confirmation do not match',
                });
            </script>";
            echo '<meta http-equiv="refresh" content="0;url=profile.php">';
            exit();
        }
    } else {
        $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'Old password is incorrect',
            });
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit();
    }
    $stmt->close();
    $conn->close();
}
if (isset($_POST['edit_pic'])) {
    if ($_FILES['profile_img']['name'] != '') {
        $file_name = $_FILES['profile_img']['name'];
        $file_temp = $_FILES['profile_img']['tmp_name'];
        $upload_dir = 'profile_img/';

        $check = getimagesize($file_temp);
        if ($check === false) {
            $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'File is not an image.',
            });
        </script>";
            echo '<meta http-equiv="refresh" content="0;url=profile.php">';
            exit();
        }

        if ($_FILES['profile_img']['size'] > 5000000) { // 5MB limit
            $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'Sorry, your file is too large. 5MB Limit',
            });
        </script>";
            echo '<meta http-equiv="refresh" content="0;url=profile.php">';
            exit();
        }

        $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
            $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.',
            });
        </script>";
            echo '<meta http-equiv="refresh" content="0;url=profile.php">';
            exit();
        }

        $unique_name = uniqid() . '.' . $imageFileType;
        $target_file = $upload_dir . $unique_name;

        if (move_uploaded_file($file_temp, $target_file)) {
            $img = $unique_name;
        } else {
            $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'Sorry, there was an error uploading your file.',
            });
        </script>";
            echo '<meta http-equiv="refresh" content="0;url=profile.php">';
            exit();
        }
    } else {
        $img = "default.jpg";
    }


    $sql = "UPDATE userinfo SET image='$img' WHERE userid='$userid'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'success',
            title: 'Profile image updated successfully.',
        });
    </script>";
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit();
    } else {
        $_SESSION['alert'] = "<script>
        Toast.fire({
            icon: 'error',
            title: 'Error updating record:  $conn->error',
        });
    </script>";
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit();
    }

    $conn->close();
}
?>

<?php include('layouts/footer.php'); ?>