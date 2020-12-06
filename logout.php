<?php
session_start();
session_destroy();
setcookie("login", "", time()-1);
setcookie("id", "", time()-1);

header('location:login.php');

?>