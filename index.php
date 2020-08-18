<?php
    session_start();
    
    if(isset($_SESSION['USR'])) {
        header("Location: account.php");
    }
    
    include "assets/scripts.php";
    
    $conn = connToDB();
    
    if (!empty($_POST['login'])) {
        if (empty($_POST['username']) || empty($_POST['password'])) {
            $loginmsg = "<div class='alert alert-danger'>Please enter all required information.</div>";
        } else {
            $user = mysqli_real_escape_string($conn, $_POST['username']);
            $pass = sha1(mysqli_real_escape_string($conn, $_POST['password']));
                        
            if (isUser($user)) {
                $loginmsg = isValidLogin($user, $pass);
            }
        }
    }
    
    if (!empty($_POST['createacct'])) {
       if (empty($_POST['desd-username']) || empty($_POST['desd-password'])) {
            $ca_msg = "<div class='alert alert-danger'>Please enter all required information.</div>";
        } else {
            $d_user = mysqli_real_escape_string($conn, $_POST['desd-username']);
            $d_pass = sha1(mysqli_real_escape_string($conn, $_POST['desd-password']));
                        
            $ca_msg = createAcct($d_user, $d_pass);
        }
    }
?><!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="assets/primary.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        <title>Log in :: Baltrac</title>
    </head>
    <body>
        <div class="container-fluid">
            <h1 class="display-3"><img src="assets/logo.png"></h1>
            <p class="lead" id="welcome-msg">
                This is a lightweight, secure, and (eventually) mobile-friendly way to keep track of your credit card balances! Don't worry, we don't store credit card numbers or CVVs on the site.
            </p>
            <hr />
            <div class="row">
                <div class="col-sm">
                    <h3>Log in</h3>
                    <?php
                        if (isset($loginmsg)) {
                            echo $loginmsg;
                        }
                    ?>
                    <form name="login-form" action="" method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username">
                        </div>
                        <div class="form-group">
                            <label for="pw">Password</label>
                            <input type="password" class="form-control" id="pw" name="password">
                        </div>
                        <input type="submit" name="login" class="btn btn-primary" value="Log in">
                    </form>
                </div>
                <br>
                <div class="col-sm">
                    <h3>Create an account</h3>
                    <?php
                        if (isset($ca_msg)) {
                            echo $ca_msg;
                        }
                    ?>
                    <form name="acct-create-form" action="" method="post">
                        <div class="form-group">
                            <label for="desd-username">Desired username</label>
                            <input type="text" class="form-control" id="desd-username" name="desd-username">
                        </div>
                        <div class="form-group">
                            <label for="desd-pw">Password</label>
                            <input type="password" class="form-control" id="desd-pw" name="desd-password">
                        </div>
                        <input type="submit" name="createacct" class="btn btn-primary" value="Create an account">
                    </form>
                </div>
            </div>
            <hr />
            &copy; 2019-20 Nathaniel Barnwell. All rights reserved.
        </div>
    </body>
</html>
    