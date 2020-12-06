<?php
require('../config.php');

$requestType = $_SERVER['REQUEST_METHOD'];

if($requestType == "GET"){
	if($_GET['url'] == 'getTasks'){
		$postBody = file_get_contents("php://input");
		if($postBody == NULL){
			$apiKey = $_GET['apiKey']; 
		}
		else{
			$postBody = json_decode($postBody, true);
			$apiKey = $postBody['apiKey'];
		}
		$users = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
		$stmt = $users->prepare("SELECT id FROM users WHERE apiKey = ? ");

		$stmt->bind_param("s", $apiKey);
		$stmt->execute();
		$result = mysqli_fetch_assoc($stmt->get_result());
		$stmt->close();
		$all_tasks = $users->query("SELECT * FROM tasks WHERE userid = $result[id] AND timeLength > 0");
		$count = mysqli_num_rows($all_tasks);
		$response = '{"Tasks":[';
		$html_response = ',"HTML":"<table><tr><th>TaskName</th><th>DueDate</th><th>Remaining Time</th><th>Priority</th></tr>';
		while($task = $all_tasks->fetch_assoc()){
			$response = $response."{";
			$response = $response.'"Task Name": "'.$task['taskName'].'",';
			$response = $response.'"Due Date": "'.$task['dueDate'].'",';
			$response = $response.'"Time Left": '.$task['timeLength'].',';
			$response = $response.'"Priority": '.$task['priority'].'';
			if(strlen($task['description']) == 0){
				$response = $response."},";
			}
			else{
				$response = $response.'"Description": "'.$task['description'].'",';
			}
			$count = $count - 1;
			if($count == 0){
				$response = rtrim($response, ", ");
			}
			$html_response.="<tr><td>$task[taskName]</td><td>$task[dueDate]</td><td>$task[timeLength]</td><td>$task[priority]</td></tr>";
		}
		$response = $response."]";
		$html_response .= '</table>"}';
		$response .= $html_response;
		
		echo $response;
		//print_r($postBody);
	}
	http_response_code(200);
}

if($requestType == "POST"){
	
	var_dump($_POST);
	http_response_code(200);
}