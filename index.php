<?php
session_start();
include('checkCookie.php');
require('config.php');
$tasks = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
?>

<!DOCTYPE html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</head>

	<body>
		<p id="dateTime"></p>
	<?php
		if(isset($_SESSION['username'])){
			echo "Welcome $_SESSION[username]<br>
			<ul>
				<li><a href='addTask.php'>Add Task</a></li>
				<li><a href='logout.php'>Logout</a></li>
			</ul>
			<div id='tasks'></div>";
		}
		else{
			echo "Anonymous user please login/register<br>
			<ul>
				<li><a href='login.php'>Login/Register</a></li>
			</ul>";
		}
	?>
	</body>
</html>

<script>
$(document).ready(function(){
$.ajax({ url: "api/getTasks",
        type:'get',
        data:{apiKey:<?php echo "'$_SESSION[apiKey]'" ?>},
        success: function(data){
           var json = $.parseJSON(data); 
           document.getElementById("tasks").innerHTML = json.HTML;
        }});
});



var currentTime = new Date();
var hours = currentTime.getHours();
var minutes = currentTime.getMinutes();
var month = currentTime.getMonth();
var day = currentTime.getDate();

var suffix = "AM";

if (hours >= 12) {
    suffix = "PM";
    hours = hours - 12;
}

if (hours == 0) {
    hours = 12;
}

if (minutes < 10) {
    minutes = "0" + minutes;
}
document.getElementById('dateTime').innerHTML += "<b>" + month + "/" + day + " " + hours + ":" + minutes + " " + suffix + "</b>";

</script>
