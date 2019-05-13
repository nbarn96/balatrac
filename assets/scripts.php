<?php
    
    /* Clerical function for connecting to the database. */
    function connToDB() {
        $conn = mysqli_connect('localhost', 'skbl17us_cbtadmn', 'u31[O-PK3{H+', 'skbl17us_cbt');
        return $conn;
    }
    
    /* Returns a value outlining if the requested username
    exists in the database. */
    function isUser($user) {
        $conn = connToDB();
        
        $query = mysqli_query($conn, "SELECT username FROM users WHERE username = '$user'");
        
        if (mysqli_num_rows($query) == 1) {
            return true;
        } else {
            return "<div class='alert alert-danger'>That username does not exist.</div>";
        }
    }
    
    /* Validates the user's login information. */
    function isValidLogin($user, $pass) {
        $conn = connToDB();
        
        $query = mysqli_query($conn, "SELECT UID, username, password FROM users WHERE username = '$user'");
        
        if (mysqli_num_rows($query) == 0) {
            return "<div class='alert alert-danger'>Your credentials are invalid.</div>";
        }
        
        $row = mysqli_fetch_assoc($query);
        
        if ($user == $row['username'] && $pass == $row['password']) {
            session_start();
            $_SESSION['USR'] = $row['username'];
            header("Location: account.php");
        } else {
            return "<div class='alert alert-danger'>Your login information is incorrect.</div>";
        }
    }
    
    /* Creates new account. */
    function createAcct($user, $pass) {
        $conn = connToDB();
        
        $query = mysqli_query($conn, "SELECT username FROM users WHERE username = '$user'");
        
        if (mysqli_num_rows($query) != 0) {
            return "<div class='alert alert-danger'>That username already exists.</div>";
        }
        
        $uid = rand(1000000, 9999999);
        
        mysqli_query($conn, "INSERT INTO users (UID, username, password) VALUES ('$uid', '$user', '$pass')");
        
        return "<div class='alert alert-success'>Your account has been created! Please log in.</div>";
    }
    
    /* Logs the user out of the active session. */
    function logOut($user) {
        session_start();
        unset($_SESSION['USR']);
        header("Location: index.php");
    }
    
    /* Returns the list of cards on the user's account. */
    function getCards($user) {
        $conn = connToDB();
        
        $visa = "<i class='fab fa-cc-visa'></i> ";
        $mastercard = "<i class='fab fa-cc-mastercard'></i> ";
        $discover = "<i class='fab fa-cc-discover'></i> ";
        $diners_club = "<i class='fab fa-cc-diners-club'></i> ";
        $amex = "<i class='fab fa-cc-amex'></i> ";
        $other = "<i class='fas fa-credit-card></i> ";
        
        $query = mysqli_query($conn, "SELECT name, type, lastfour, balance, CID, credit_limit FROM card WHERE user = '$user'");
        
        if (mysqli_num_rows($query) == 0) {
            echo "<tr>";
            echo "<th colspan='4'>You have no cards on record.</th>";
            echo "</tr>";
        }
        
        while($row = mysqli_fetch_array($query)) {
            echo "<tr>";
            echo "<th>".$row['name']."</th>";
            if ($row['type'] == "Visa") {
                echo "<td>$visa ";
            } else if ($row['type'] == "Discover") {
                echo "<td>$discover ";
            } else if ($row['type'] == "MasterCard") {
                echo "<td>$mastercard ";
            } else if ($row['type'] == "American Express") {
                echo "<td>$amex ";
            } else if ($row['type'] == "Diners' Club") {
                echo "<td>$diners_club ";
            } else {
                echo "<td>$other ";
            }
            echo " ends in ".$row['lastfour']."</td>";
            echo "<td><b>$".number_format($row['balance'], 2)."</b> out of $".number_format($row['credit_limit'], 0)."</td>";
            echo "<td>";
                echo "<form name='delete-card' action='' method='post'>";
                echo "<input type='hidden' id='CID' name='CID' value='".$row['CID']."'>";
                echo "<input type='submit' name='delete' value='Delete' class='btn btn-danger'>";
                echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    }
    
    /* Gets a list of card names belonging to a specific user. */
    function balanceUpdateForm($user) {
        $conn = connToDB();
        
        $query = mysqli_query($conn, "SELECT name, balance, CID FROM card WHERE user = '$user'");
        
        if (mysqli_num_rows($query) == 0) {
            echo "There are no cards on file.";
            return 0;
        } else {
            echo "<form action='' name='bal-update' method='post'>";
                echo "<div class='form-row'>";
                    echo "<div class='form-group col-md-6'>";
                        echo "<select class='form-control' id='CID' name='CID'>";
                        while ($row = mysqli_fetch_array($query)) {
                            echo "<option value='".$row['CID']."'>";
                            echo $row['name'];
                            echo "</option>";
                        }
                        echo "</select>";
                    echo "</div>";
                    echo "<div class='form-group col-md-3'>";
                        echo "<input type='text' name='amt' id='amt' class='form-control' placeholder='Amount'>";
                    echo "</div>";
                    echo "<div class='form-group col-md-3'>";
                        echo "<input class='btn btn-primary' id='update' type='submit' name='update' value='Update'>";
                    echo "</div>";
                echo "</div>";
            echo "</form>";
        }
    }
    
    /* Deletes the given card from the database. */
    function deleteCard($CID) {
        $conn = connToDB();
        
        mysqli_query($conn, "DELETE FROM card WHERE CID = $CID");
        
        return "<div class='alert alert-success'>The card has been deleted.</div>";
    }
    
    /* Updates a card's balance. */
    function updateBalance($CID, $amt) {
        $conn = connToDB();
        
        $existing_bal = mysqli_query($conn, "SELECT balance FROM card WHERE CID = '$CID'");
        
        $r1 = mysqli_fetch_assoc($existing_bal);
        
        $new_amt = $amt;
        
        $query = mysqli_query($conn, "UPDATE card SET balance = '$new_amt' WHERE CID = '$CID'");
        
        if ($new_amt < $r1['balance'])
            return "<div class='alert alert-success'>The card's balance has been reduced.</div>";
        else
            return "<div class='alert alert-success'>The card's balance has been increased.</div>";
    }
    
    /* Adds a new card to the user's portfolio of cards. */
    function addNewCard($name, $user, $ctype, $lf, $climit, $bal) {
        $conn = connToDB();
        
        $query = mysqli_query($conn, "SELECT name FROM card WHERE name = '$name'");
        
        if (mysqli_num_rows($query) != 0) {
            return "<div class='alert alert-danger'>That card already exists.</div>";
        }
        
        $cid = rand(1000000, 9999999);
        
        mysqli_query($conn, "INSERT INTO card (CID, user, name, type, lastfour, credit_limit, balance) VALUES ('$cid', '$user', '$name', '$ctype', '$lf', '$climit', '$bal')");
        
        return "<div class='alert alert-success'>The card has been added.</div>";
    }

?>