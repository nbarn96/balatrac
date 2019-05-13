<?php
    session_start();
    
    if(!isset($_SESSION['USR'])) {
        header("Location: index.php");
    }
    
    $usrname = $_SESSION['USR'];
    
    include "assets/scripts.php";
    
    $conn = connToDB();
    
    if (!empty($_POST['logout'])) {
        logOut($usrname);
    }
    
    if (!empty($_POST['update'])) {
        $cid = $_POST['CID'];
        $amt = mysqli_real_escape_string($conn, $_POST['amt']);
        $bal_msg = updateBalance($cid, $amt);
    }
    
    if (!empty($_POST['add'])) {
        $cname = mysqli_real_escape_string($conn, $_POST['card-name']);
        $ctype = $_POST['type'];
        $lastfour = mysqli_real_escape_string($conn, $_POST['lastfour']);
        $climit = mysqli_real_escape_string($conn, $_POST['cred_limit']);
        $bal = mysqli_real_escape_string($conn, $_POST['bal']);
        $anc_msg = addNewCard($cname, $usrname, $ctype, $lastfour, $climit, $bal);
    }
?><!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="assets/primary.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
        <title>Credit card balance tracker :: skbl17.net</title>
    </head>
    <body>
        <div class="container-fluid">
            <nav class="navbar navbar-light bg-light">
                <p class="my-2 my-lg-0">
                    You are currently logged in as <b><?php echo $usrname; ?></b>
                </p>
                <form action="" method="post">
                    <input type="submit" value="Logout" class="btn btn-danger" name="logout" />
                </form>
            </nav>
            <h1 class="display-3">Credit card balance tracker</h1>
            <p class="lead" id="welcome-msg">
                This is a lightweight, secure, and (eventually) mobile-friendly way to keep track of your credit card balances! Don't worry, we don't store credit card numbers or CVVs on the site.
            </p>
            <h2>Your credit cards</h2>
            <br>
            <?php
                if (!empty($_POST['delete'])) {
                    $msg = deleteCard($_POST['CID']);
                }
                
                if (isset($msg)) {
                    echo $msg;
                }
            ?>
                <div class="row">
                    <table class="table" id="bals">
                        <tbody>
                            <?php
                                getCards($usrname);
                            ?>
                        </tbody>
                    </table>
                </div>
            <hr />
                <h2>Update card balance</h2>
                <p>Select a card and enter the card's new balance.</p>
                <div class="row">
                    <div class="col-sm">
                        <?php
                            if (isset($bal_msg)) {
                                echo $bal_msg;  
                            }
                            balanceUpdateForm($usrname);
                        ?>
                    </div>
                </div>
            <hr />
                <h2>Add a new card</h2>
                <p>
                    Use this form to add a card to your collection. <b>All fields are required.</b>
                </p>
                <div class="row">
                    <div class="col-sm">
                    <?php
                        if (isset($anc_msg)) {
                            echo $anc_msg;
                        }
                    ?>
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="card-name">Name of card</label>
                                <input type="text" id="card-name" name="card-name" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Card type</label>
                                <select class="form-control" id="type" name="type">
                                    <option>Visa</option>
                                    <option>MasterCard</option>
                                    <option>Discover</option>
                                    <option>American Express</option>
                                    <option>Diners' Club</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="lastfour">Last 4 digits on card</label>
                                <input type="text" id="lastfour" name="lastfour" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="limit">Credit limit</label>
                                <input type="text" id="limit" name="cred_limit" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="balance">Starting balance</label>
                                <input type="text" id="balance" name="bal" class="form-control">
                            </div>
                        </div>
                        <input class="btn btn-primary" name="add" type="submit" value="Add card">
                    </form>
                    </div>
                </div>
            <hr />
            &copy; 2019 <a href="https://www.nbarn.me">Nathaniel Barnwell</a>. All rights reserved.
        </div>
    </body>
</html>