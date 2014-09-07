<?php
	//initialize session
	session_start();
	// regenerates a new session_id to help prevent session hijacking
    session_regenerate_id();
	
	if(!(isset($_SESSION['username'], $_SESSION['login_string']) || isset($_COOKIE['username']))){
		// refuse user to see this page if not logged in
		header("Location: index.html");
		exit();
	}
?>
<body>
	<p>Welcome <?php echo $_SESSION['username']; ?></p>
	<a href='logout.php'>Log out</a>
</body>