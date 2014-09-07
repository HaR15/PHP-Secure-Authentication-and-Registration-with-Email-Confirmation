<?php
    // initialize session to create $_SESSION variables
    session_start();
    // regenerates a new session_id to help prevent session hijacking
    session_regenerate_id();
  
    // if already logged in, head over to welcome.php page instead
    if(isset($_SESSION['username'], $_SESSION['login_string']) || isset($_COOKIE['username'])){
        header("Location: welcome.php");
        exit();
    }
    
    include("db_connection.php");
    
    if(isset($_POST['login'])) {
        // Form submit button clicked
        
        //get username and password
        $username = mysqli_real_escape_string($link, $_POST['username']);
        $password = mysqli_real_escape_string($link, $_POST['password']);
        
        if($username && $password){
            //form was completed
            
            if(!($result = mysqli_query($link, "SELECT id, Username, Email, Password, Activation, Salt FROM LoginPage WHERE Username = '$username' AND Activation IS NULL"))){
                echo ("Error: " .  mysqli_error($link));
            }
            // get back query data as an associative array
            $dbData = mysqli_fetch_array($result, MYSQL_ASSOC);
            $count = mysqli_num_rows($result);
            
            if($count == 1){
                // user exists and is activated, check password now
                
                if(checkbrute($dbData['id'], $link)){
                    die("Bruteforcing. Blocked");
                    exit();
                }
                
                // get salt generated for user stored in db
                $dbSalt = $dbData['Salt'];
                // sha512 hash inputted password with salt from db
                $password = hash('sha512', $password . $dbSalt);
                
                if($password === $dbData['Password']){
                    //password matched one in db
                    if($_POST['rememberMe']){
                        //Remember me was checked off
                        setCookie('username', $username, time()+3600);
                    }
                    else{
                        //User does not want to be remembered
                        $_SESSION['username'] = $username;
                        // storing HTTP USER AGENT (info about browser)
                        /// in session variable will help prevent session hijacking
                        // when isset($_SESSION['login_string']) is checked during login confirmation
                        $_SESSION['login_string'] = hash('sha512', $password . $_SERVER['HTTP_USER_AGENT']);
                    }
                    // user validated, head to welcome page
                    header("Location: welcome.php");
                    exit();
                }else{
                    //Incorrect password
                    $id = $dbData['id'];
                    $now = time();
                    $query = "INSERT INTO `loginAttempts`(id, time) VALUES ('$id','$now')";
                    if(!($result = mysqli_query($link, $query))){
                        echo "Error: " . mysqli_error($link);
                    }
                    die("Password is incorrect");
                    exit();
                }
            }
            else{
                // Incorrect username
                die("User does not exist or is not yet activated");
                exit();
            }
        }
        else{
            //incomplete form
            die("Form fields are incomplete.");
            exit();
        }
    }
    
    function checkbrute($id, $link){
        $now = time();
        $validAttempts = $now - (3600);
        if(!($result = mysqli_query($link, "SELECT * FROM `loginAttempts` WHERE id = '$id' AND time > '$validAttempts'"))){
            echo "Error: " . mysqli_error($link);
        }
        $num_rows = mysqli_num_rows($result);
        return $num_rows > 5 ? true : false;
    }
    
    // close my_db connection
    mysqli_close($link);
?>