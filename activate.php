<?php
    include("db_connection.php");
    
    if(isset($_GET['email']) && preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_GET['email']) ){
        //valid email sent in response
        $email = $_GET['email'];
    }
    if(isset($_GET['key']) && (strlen($_GET['key']) == 32)){
        //valid activation key sent in response
        $activationKey = $_GET['key'];
    }
    
    if($email && $activationKey){
        //email and activation key passed into GET response
        
        //check if activation time expired
        if(!isExpired()){
            //not expired, can activate
            
            // Update Activation key to NULL for active
            $query = "UPDATE LoginPage SET Activation=NULL WHERE Email = '$email' AND Activation = '$activationKey'";
            if(!($result = mysqli_query($link, $query))){
                echo "Error: " . mysqli_error($link);
            }
            echo "<div>The account is activated.</div>";
            echo "<div>Proceed to login <a href='index.html'>here</a></div>";    
        }
        else{
            echo "Time for activation expired";
        }
    }
    else{
        // something went wrong
        echo "Error in activation";
    }
    
    // checks if activation time is expired
    function isExpired(){
        // get a tuple if current time is past expiration time for this email address
        $result = mysqli_query($link, "SELECT * FROM LoginPage WHERE Email = '$email' AND NOW() > ExpirationTime");
        $count = mysqli_num_rows($result);
        // return true if expired, else false            
        return $count > 0 ? true : false;
    }
    
    //close mysql connection
    mysqli_close($link);
    
?>