<?php
require('../config.php');

$requestType = $_SERVER['REQUEST_METHOD'];

if($requestType == "GET"){
	if($_GET['url'] == 'getTasks'){
		$apiKey = $_GET['apiKey']; 
		$users = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
		$stmt = $users->prepare("SELECT id FROM users WHERE apiKey = ? ");

		$stmt->bind_param("s", $apiKey);
		$stmt->execute();
		$result = mysqli_fetch_assoc($stmt->get_result());
		$stmt->close();
		$all_tasks = $users->query("SELECT * FROM tasks WHERE userid = $result[id] AND timeLength > 0");
		$count = mysqli_num_rows($all_tasks);
		$response = '{"Tasks":[';
		$html_response = ',"HTML":"';
		$counter = 1;
		while($task = $all_tasks->fetch_assoc()){
			$response = $response."{";
			$response = $response.'"Task Name": "'.$task['taskName'].'",';
			$response = $response.'"Due Date": "'.$task['dueDate'].'",';
			$response = $response.'"Time Left": '.$task['timeLength'].',';
			$response = $response.'"Priority": '.$task['priority'].'';
			if(strlen($task['description']) == 0){
				if($count == 1){
					$response = $response.",";
				}
				else{
					$response = $response."},";
				}
			}
			else{
				if($count == 1){
					$response = $response.',"Description": "'.$task['description'].'",';
				}
				else{
					$response = $response.',"Description": "'.$task['description'].'"},';
				}
				
			}
			if($counter == 1){
				$html_response.= "<div class='carousel-item active'>";
			}
			else{
				$html_response.= "<div class='carousel-item'>";
			}
			$html_response.= "<h1 id='task1Name' style='color: rgb(252,252,252);font-size: 20px;text-align: center;'>$task[taskName]</h1><p style='color: rgb(221,210,235);text-align: center;'>Due Date- $task[dueDate]<br>Time Remaining- $task[timeLength] Hours<br><a class='btn btn-primary btn-sm' role='button' data-toggle='modal' href='#editTask$counter'>Edit</a><br><br></p></div>";
			$count = $count - 1;
			$counter = $counter + 1;
			if($count == 0){
				$response = rtrim($response, ", ");
			}
		}
		$response = $response."}]";
		$html_response .= '"}';
		$response .= $html_response;
		
		echo $response;
		//print_r($postBody);
	}
	http_response_code(200);
}

if($requestType == "POST"){
	if($_GET['url'] == 'addTask'){
		$apiKey = $_POST['apiKey'];
		$taskName = $_POST['taskName'];
		$sqlDate = explode("/",$_POST['dueDate']);
		$dueDate = $sqlDate[2].'-'.$sqlDate[0].'-'.$sqlDate[1];
		$taskLength = $_POST['completionTime'];
		$priority = $_POST['priority'];
		$description = $_POST['description'];
		if(strlen($description) == 0){
			$description = "";
		}
		$users = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
		$stmt = $users->prepare("SELECT id FROM users WHERE apiKey = ? ");

		$stmt->bind_param("s", $apiKey);
		$stmt->execute();
		$result = mysqli_fetch_assoc($stmt->get_result());
		$stmt->close();

		$stmt = $users->prepare("INSERT INTO `tasks` (`taskName`, `dueDate`, `timeLength`, `priority`, `description`, `userid`) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssiisi", $taskName, $dueDate, $taskLength, $priority, $description, $result['id']);
		$check = $stmt->execute();
		if(!$check){
			http_response_code(405);
			exit();
		}

	}

	http_response_code(200);
}