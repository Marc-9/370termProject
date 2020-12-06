<?php
session_start();
include('checkCookie.php');

if($_SERVER['REQUEST_METHOD'] == "POST"){
	require('config.php');
	$tasks = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
	$taskName = $_POST['taskName'];
	$sqlDate = explode("/",$_POST['dueDate']);
	$dueDate = $sqlDate[2].'-'.$sqlDate[0].'-'.$sqlDate[1];
	$length = $_POST['lengthHours'];
	$priority = $_POST['priority'];
	$comment = $_POST['comment'];
	if(strlen($comment) == 0){
		$comment = "";
	}
	$tasks->query("INSERT INTO `tasks` (`taskName`, `dueDate`, `timeLength`, `priority`, `description`, `userid`) VALUES ('$taskName', '$dueDate', $length, $priority, '$comment', $_SESSION[id])");


}

?>
<!DOCTYPE html>
	<head>
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	</head>
	<body>
		<form id="newTask" action="addTask.php" method="POST">
		  <label class="required" for="taskName">Task Name:</label>
		  <input type="text" id="taskName" name="taskName" required><br>
		  <label class="required" for="dueDate">Due Date:</label>
		  <input id="dueDate" name="dueDate" type="text" required><br>

		  <label for="lengthHours" value="2" class="required">Completion length (hours):</label>
		  <div class="range-wrap">
  		  	<input type="range" class="range" min="1" max="15" id="lengthHours" name="lengthHours" required><br>
  			<output class="bubble"></output>
		  </div>

		  <label for="priority" class="required">Priority:</label>
		  <div class="range-wrap">
  		  	<input type="range" class="range" min="1" max="10" id="priority" name="priority" required><br>
  			<output class="bubble"></output>
		  </div>

		  <label for="comment">Comments</label><br>
		  <textarea name="comment" form="newTask" placeholder="Enter text here..."></textarea><br>
		  <input type="submit" value="Submit">
		</form>
	</body>

</html>
<script>
	$( function() {
    $( "#dueDate" ).datepicker();
  } );

const allRanges = document.querySelectorAll(".range-wrap");
allRanges.forEach(wrap => {
  const range = wrap.querySelector(".range");
  const bubble = wrap.querySelector(".bubble");

  range.addEventListener("input", () => {
    setBubble(range, bubble);
  });
  setBubble(range, bubble);
});

function setBubble(range, bubble) {
  const val = range.value;
  const min = range.min ? range.min : 0;
  const max = range.max ? range.max : 100;
  const newVal = Number(((val - min) * 100) / (max - min));
  bubble.innerHTML = val;

  bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
}
</script>
<style>
  .required:after {
    content:" *";
    color: red;
  }
  .range-wrap {
  position: relative;
  margin: 0 auto 1rem;
}

.bubble {
  background: red;
  color: white;
  padding: 4px 12px;
  border-radius: 4px;
  left: 50%;
  transform: translateX(-50%);
}
.bubble::after {
  content: "";
  width: 2px;
  height: 2px;
  background: red;
  top: -1px;
  left: 50%;
}
</style>
