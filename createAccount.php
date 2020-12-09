<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('location:login.php');
    exit();
}
function set_cookie($name, $pass){
    $name_length = strlen($name);
    $pass_length = strlen($pass);
    $val = substr($name,1,$name_length/2).substr($pass, 1,$pass_length/2).rand(1,1000);
    $cookie = password_hash($val, PASSWORD_DEFAULT);
    setcookie("login",$cookie, time()+31536000);
    return password_hash($cookie, PASSWORD_DEFAULT);
}
function generateRandomString($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
require_once('config.php');
$users = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
if(!isset($_POST['user']) || !isset($_POST['password'])){
    header('location:login.php');
    exit();
}

$name = htmlspecialchars(trim($_POST['user']));
$pass = $_POST['password'];

$stmt = $users->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0){
    $stmt->close();
    $users->close();
    header('location:login.php?error=exists');
    exit();
}



$ins_cookie = set_cookie($name,$pass);
$new_password = password_hash($pass, PASSWORD_DEFAULT);
$apiKey = generateRandomString();
$stmt = $users->prepare("INSERT INTO `users` (`username`, `password`, cookie, apiKey ) VALUES ( ?, ?, ?, ?)");
$stmt->bind_param("ssss", $name,$new_password, $ins_cookie, $apiKey);
// Insertion was a success
if($stmt->execute()){
    $date = date('Y-m-d H:i:s');
    $users->query("UPDATE users SET lastLogin = '$date' WHERE id = $stmt->insert_id ");
    $users->close();
    setcookie("id", $stmt->insert_id,time()+31536000);
    $stmt->close();
    header('location:index.php');
}
// Really no reason for it to fail but its here when I was debugging
else{
    $users->close();
    $stmt->close();
    header('location:login.php?error=5');
}

?>