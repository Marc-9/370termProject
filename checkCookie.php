<?php
if(   isset($_COOKIE['login']) && (!isset($_SESSION['id']) || !isset($_SESSION['canvasAPI'])) && isset($_COOKIE['id'])){
    require('config.php');
    $user = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_DATABASE1);
    $stmt = $user->prepare("SELECT username,id,cookie,apiKey FROM users WHERE id = ?");
    $stmt->bind_param("s", $_COOKIE['id']);
    $stmt->execute();
    $check = mysqli_fetch_assoc($stmt->get_result());

    if(password_verify($_COOKIE['login'], $check['cookie'])){
        $canvasAPI = $user->query("SELECT canvasURL, canvasAPI FROM user_school WHERE userid = $_COOKIE[id]");
        if(mysqli_num_rows($canvasAPI) > 0){
            $canvasData = mysqli_fetch_assoc($canvasAPI);
            $_SESSION['canvasAPI'] = $canvasData['canvasAPI'];
            $_SESSION['canvasURL'] = $canvasData['canvasURL'];
        }
        $_SESSION['id'] = $check['id'];
        $_SESSION['username'] = $check['username'];
         $_SESSION['apiKey'] = $check['apiKey'];
        $date = date('Y-m-d H:i:s');
        $user->query("UPDATE users SET last_login = '$date' WHERE id = ".$_SESSION['id']." ");
    }
    $user->close();
}

?>