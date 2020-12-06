<!DOCTYPE html>
    <head>
        <title>Login</title>
        <meta charset="utf-8">

        </head>

        <body>
            <!-- First Row -->
            <h1 id="login">Login</h1>
            <form action="validation.php" method="post">
                <p> 
                	<label>Username</label>
					<input type="text" name="user" size="20" required>
                    <br>
                    <br>
                    
					<label>Password</label>
					<input type="password" name="password" class="form-control" required>					
                    <br>
                </p>
                    <button type="submit">Login</button>
                    <?php if(isset($_GET['error'])){
						if($_GET['error'] == 'login'){
							echo "<p>Invalid Username or password</p>";
						}
						else if($_GET['error'] == 'account'){
							echo "<p>Username Provided does not exist</p>";
						}
					}
					?>
			</form>
			
			<h1 id="Register">Register</h1>
            <form action="registration.php" method="post">
                <p> 
                	<label>Username</label>
					<input type="text" name="user" size="20" required>
                    <br>
                    <br>
                    
					<label>Password</label>
					<input type="password" name="password" class="form-control" required>					
                    <br>
                </p>
                    <button type="submit">Register</button>
                    <?php if(isset($_GET['error'])){
						if($_GET['error'] == 'exists'){
							echo "<p>Username already Exists</p>";
						}
					}
					?>
			</form>
                        
            </body>
</html>
