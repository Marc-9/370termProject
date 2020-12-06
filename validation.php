<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('location:login.php');
    exit();
}
require('config.php');
function set_cookie($name, $pass){
	$name_length = strlen($name);
	$pass_length = strlen($pass);
	$val = substr($name,1,$name_length/2).substr($pass, 1,$pass_length/2).rand(1,1000);
	$cookie = password_hash($val, PASSWORD_DEFAULT);
	setcookie("login",$cookie, time()+31536000);
	return password_hash($cookie, PASSWORD_DEFAULT);
}

$users = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
if(!isset($_POST['user']) || !isset($_POST['password'])){
    header('location:login.php');
    exit();
}

$name = htmlspecialchars(trim($_POST['user']));
$pass = $_POST['password'];

$stmt = $users->prepare("SELECT id,password FROM users WHERE username = ? ");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = mysqli_fetch_assoc($stmt->get_result());
$stmt->close();

if($result == NULL){
	header('location:login.php?error=account');
	exit();
}

if(password_verify($pass, $result['password'])){
	$ins_cookie = set_cookie($name,$pass);
	setcookie("id", $result['id'],time()+31536000);
	$date = date('Y-m-d H:i:s');
	$users->query("UPDATE users SET cookie = '$ins_cookie', lastLogin = '$date' WHERE id = ".$result['id']." ");
	$users->close();
    header('location:index.php');
    exit();
}
else{
    header('location:login.php?error=login');
    $users->close();
    exit();
}

?>