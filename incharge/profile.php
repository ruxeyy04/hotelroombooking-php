<?php include('layouts/header.php') ?>
<div class="main p-3">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="dashboard_header mb_50">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="dashboard_header_title">
                            <h3> Profile</h3>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex justify-content-center flex-column flex-md-row text-center text-md-start mb-3">
                <figure class="me-4 flex-shrink-0">
                    <div style="background-image: url('/profile_img/<?= $userinfo['image'] == null ? 'default.jpg' : $userinfo['image'] ?>'); background-position: center;
                                background-size: cover;
                                height: 100px;
                                width: 100px;
                                border-radius: 50%;
                                box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;">
                    </div>
                </figure>
                <div class="flex-fill">
                    <h5 class="mb-3"><?= $userinfo['fname'] ?> <?= $userinfo['lname'] ?></h5>
                    <?php
                    if (isset($_GET['editimage']) != 1) { ?>
                        <a href="profile.php?editimage=1" class="btn btn-primary me-2">Edit Image</a>
                    <?php } else { ?>
                        <form action="" method="post" enctype="multipart/form-data" class="d-flex align-items-center">
                            <div class="">
                                <input class="form-control" type="file" id="formFile" name="profile_img">
                            </div>
                            <div class="d-flex align-items-center justify-content-center ms-2">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-danger" onclick="location.replace('profile.php')">Cancel</button>
                                    <button type="submit" class="btn btn-success" name="edit_pic">Update Image</button>
                                </div>
                            </div>
                        </form>
                    <?php }
                    ?>
                </div>
                <?php
                if (isset($_POST['edit_pic'])) {
                    if ($_FILES['profile_img']['name'] != '') {
                        $file_name = $_FILES['profile_img']['name'];
                        $file_temp = $_FILES['profile_img']['tmp_name'];
                        $upload_dir = '../profile_img/';

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
                                    title: 'Error updating record: $conn->error',
                                });
                            </script>";
                        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
                    }

                    $conn->close();
                }
                ?>
            </div>
            <form action="" method="post">
                <div class="mb-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title mb-4">Basic Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="fname" class="form-control" value="<?= $userinfo['fname'] ?> " required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" name="midname" class="form-control" value="<?= $userinfo['midname'] ?> " required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="lname" class="form-control" value="<?= $userinfo['lname'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" value="<?= $userinfo['username'] ?>" required>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="text" name="email" class="form-control" value="<?= $userinfo['email'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="phone" value="<?= $userinfo['contact'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select name="gender" class="form-select" required>
                                            <option value="" disabled selected> - Select Gender - </option>
                                            <option value="Male" <?= $userinfo['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= $userinfo['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control" value="<?= $userinfo['usertype'] ?>" readonly disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" name="updateinfo">Save Profile</button>
            </form>

            <form action="" method="post">
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Change Password</h6>
                        <div class="mb-3">
                            <label class="form-label">Old Password</label>
                            <input type="password" class="form-control" name="oldpass" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="newpass" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password Repeat</label>
                            <input type="password" class="form-control" name="confirmpass" required>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-4" type="submit" name="updatepassword">Change Password</button>
            </form>
        </div>
    </div>
</div>
<?php

if (isset($_POST['updateinfo'])) {
    // Retrieve form data
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $midname = mysqli_real_escape_string($conn, $_POST['midname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Update userinfo table
    $sql_profile = "
        UPDATE userinfo 
        SET fname = ?, midname = ?, lname = ?, contact = ?, gender = ?
        WHERE userid = ?";

    $stmt_profile = $conn->prepare($sql_profile);
    $stmt_profile->bind_param("sssssi", $fname, $midname, $lname, $phone, $gender, $userid);

    if ($stmt_profile->execute()) {
        $sql_info = "
            UPDATE userinfo 
            SET email = ?, username = ?
            WHERE userid = ?";

        $stmt_info = $conn->prepare($sql_info);
        $stmt_info->bind_param("ssi", $email, $username, $userid);

        if ($stmt_info->execute()) {
            $_SESSION['alert'] = "<script>
                Toast.fire({
                    icon: 'success',
                    title: 'Profile info updated successfully.',
                });
            </script>";
            echo '<meta http-equiv="refresh" content="0;url=profile.php">';
            exit();
        } else {
            $_SESSION['alert'] = "<script>
                Toast.fire({
                    icon: 'error',
                    title: 'Error updating customer info: " . $conn->error . "',
                });
            </script>";
            echo '<meta http-equiv="refresh" content="0;url=profile.php">';
            exit();
        }
        $stmt_info->close();
    } else {
        $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'Error updating profile info: " . $conn->error . "',
            });
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit();
    }
    $stmt_profile->close();
    $conn->close();
}

if (isset($_POST['updatepassword'])) {
    $oldpass = mysqli_real_escape_string($conn, $_POST['oldpass']);
    $newpass = mysqli_real_escape_string($conn, $_POST['newpass']);
    $confirmpass = mysqli_real_escape_string($conn, $_POST['confirmpass']);

    // Retrieve the current password from the database
    $sql_get_password = "SELECT password FROM userinfo WHERE userid = ?";
    $stmt = $conn->prepare($sql_get_password);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $current_password = $row['password']; // Retrieved password from the database

    // Verify the old password
    if ($oldpass === $current_password) {
        // Check if the new password and confirm password match
        if ($newpass === $confirmpass) {
            // Update the password in the database without hashing
            $sql_update_password = "UPDATE userinfo SET password = ? WHERE userid = ?";
            $update_stmt = $conn->prepare($sql_update_password);
            $update_stmt->bind_param("si", $newpass, $userid);

            if ($update_stmt->execute()) {
                $_SESSION['alert'] = "<script>
                    Toast.fire({
                        icon: 'success',
                        title: 'Password successfully updated',
                    });
                </script>";
                echo '<meta http-equiv="refresh" content="0;url=profile.php">';
                exit();
            } else {
                $_SESSION['alert'] = "<script>
                    Toast.fire({
                        icon: 'error',
                        title: 'Error updating password: {$conn->error}',
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
                    title: 'New password and confirm password do not match',
                });
            </script>";
            echo '<meta http-equiv="refresh" content="0;url=profile.php">';
            exit();
        }
    } else {
        $_SESSION['alert'] = "<script>
            Toast.fire({
                icon: 'error',
                title: 'Incorrect old password',
            });
        </script>";
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit();
    }
    $stmt->close();
    $conn->close();
}
?>
<?php include('layouts/footer.php') ?>