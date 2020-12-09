<?php
require('../config.php');
$requestType = $_SERVER['REQUEST_METHOD'];

if(isset($_GET['apiKey'])){
	$apiKey = $_GET['apiKey'];
}
else if(isset($_POST['apiKey'])){
	$apiKey = $_POST['apiKey'];
}
else{
	http_response_code(401);
	exit();
}
$users = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
$stmt = $users->prepare("SELECT id FROM users WHERE apiKey = ? ");
$stmt->bind_param("s", $apiKey);
$stmt->execute();
$result = mysqli_fetch_assoc($stmt->get_result());
$stmt->close();
if(!isset($result['id'])){
	http_response_code(401);
	exit();
}

if($requestType == "GET"){
	if($_GET['url'] == 'getTasks'){
		$all_tasks = $users->query("SELECT * FROM tasks WHERE userid = $result[id] AND timeLength > 0 ORDER BY ordered");
		$count = mysqli_num_rows($all_tasks);
		$response = '{"Tasks":[';
		$html_response = ',"HTML":"';
		$carousel_response = '"Carasoul":"';
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
				$desc =  str_replace(array("\n", "\r"), '', $task['description']);
				if($count == 1){
					$response = $response.',"Description": "'.$desc.'",';
				}
				else{
					$response = $response.',"Description": "'.$desc.'"},';
				}
				
			}
			if($counter == 1){
				$html_response.= "<div class='carousel-item active'>";
				$carousel_response .= "<li data-target='#carousel-1' data-slide-to='0' class='active'></li>";
			}
			else{
				$html_response.= "<div class='carousel-item'>";
				$counterminus = $counter -1;
				$carousel_response .= "<li data-target='#carousel-1' data-slide-to='$counterminus' class='active'></li>";
			}
			$html_response.= "<h1 id='task1Name' style='color: rgb(252,252,252);font-size: 20px;text-align: center;'>$task[taskName]</h1><p style='color: rgb(221,210,235);text-align: center;'>Due Date- $task[dueDate]<br>Time Remaining- $task[timeLength] Hours<br><a class='btn btn-primary btn-sm' role='button' data-toggle='modal' onclick='editTask($task[id])'>Edit</a><br><br></p></div>";
			$count = $count - 1;
			$counter = $counter + 1;
			if($count == 0){
				$response = rtrim($response, ", ");
			}
		}
		if(mysqli_num_rows($all_tasks) > 0){
			$response = $response."}";
		}
		$response = $response."]";
		$html_response .= '",';
		$carousel_response .= '"}';
		$response .= $html_response;
		$response .= $carousel_response;
		
		echo $response;
	}

	if($_GET['url'] == 'getUserInfo'){
		$userData = '{"Data": {';
		$currentData = mysqli_fetch_assoc($users->query("SELECT SUM(`timeLength`) as TOTHOURS, COUNT(`id`) AS TOTTASKS FROM tasks WHERE `userid` = $result[id] AND timeLength > 0"));
		$totData = mysqli_fetch_assoc($users->query("SELECT SUM(`timeLength`) as TOTHOURS, COUNT(`id`) AS TOTTASKS FROM tasks WHERE `userid` = $result[id] AND timeLength = 0"));
		if($currentData['TOTHOURS'] == null){
			$userData .= '"Hours Remaining" : 0,';
		}
		else{
			$userData .= '"Hours Remaining" : '.$currentData['TOTHOURS'].',';
		}
		if($totData['TOTHOURS'] == null){
			$userData .= '"Hours Completed" : 0,';
		}
		else{
			$userData .= '"Hours Completed" : '.$totData['TOTHOURS'].',';
		}
		$schoolCheck = $users->query("SELECT * FROM user_school WHERE userid = $result[id]");
		$userData .= '"Tasks Remaining" : '.$currentData['TOTTASKS'].',';
		$userData .= '"Tasks Completed" : '.$totData['TOTTASKS'];
		$userData .= "},";
		if(mysqli_num_rows($schoolCheck) > 0){
			$schoolData = $schoolCheck->fetch_assoc();
			$userData .= '"School Information": {';
			$userData .= '"School Name": "'.$schoolData['schoolName'].'",';
			if(isset($schoolData['startYear'])){
				$userData .= '"Start Year": "'.$schoolData['startYear'].'",';
			}
			if(isset($schoolData['endYear'])){
				$userData .= '"End Year": "'.$schoolData['endYear'].'",';
			}
			if(isset($schoolData['canvasURL'])){
				$userData .= '"Canvas URL": "'.$schoolData['canvasURL'].'",';
			}
			if(isset($schoolData['canvasAPI'])){
				$userData .= '"Api Key": "'.$schoolData['canvasAPI'].'",';
			}
			$userData = rtrim($userData, ", ");
			$userData .= "}";
			session_start();
			$_SESSION['canvasURL'] = $schoolData['canvasURL'];
			$_SESSION['canvasAPI'] = $schoolData['canvasAPI'];

		}
		else{
			$userData .= '"School Information": {}';
		}
		$userData .= "}";
		echo $userData;

	}

	if($_GET['url'] == 'getCanvas'){

		$curl = curl_init();
		$url = $_GET['Canvas_URL'];
		$apiKey = $_GET['API_Key'];

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url.'/api/v1/courses?enrollment_type=student&enrollment_state=active&state%5B%5D=available',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    'Accept: application/json',
		    'Referer: '.$url,
		    'Accept-Language: en-us',
		    'Host: colostate.instructure.com',
		    'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.3 Safari/605.1.15',
		    'Authorization: Bearer '.$apiKey,
		    'Connection: keep-alive'
		  ),
		));

		$response = curl_exec($curl);
		$classes = json_decode($response);
		curl_close($curl);
		$classNames = array();
		$classIds = array();

		$responseAPI = '{"Classes":[';
		foreach ($classes as $class) {
			
			$check = json_encode($class);
			$class = json_decode($check, true);
			$curl = curl_init();
			$responseAPI .= '{"';
			$responseAPI .= "$class[name]";
			$responseAPI .= '":[';
			

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url.'/api/v1/courses/'.$class['id'].'/assignments?bucket=upcoming&per_page=100',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
			    'Accept: application/json',
			    'Referer: '.$url,
			    'Accept-Language: en-us',
			    'Host: colostate.instructure.com',
			    'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.3 Safari/605.1.15',
			    'Authorization: Bearer '.$apiKey,
			    'Connection: keep-alive'
			  ),
			));

			$response2 = curl_exec($curl);
			$indivassignments = json_decode($response2);
			curl_close($curl);


			foreach ($indivassignments as $assignment) {
				$check = json_encode($assignment);
				$assignment = json_decode($check, true);
				$taskName =  $assignment['name'];
				$dueDate = substr($assignment['due_at'], 0, 10);
				$points = $assignment['points_possible'];
				preg_match('/<p>(.*?)<\/p>/s', $assignment['description'], $matches);
				$responseAPI .= "{";
				if(count($matches) > 0){
					$description =  strip_tags($matches[0]);
					$responseAPI .= '"Description" : "';
					$responseAPI .= $description;
					$responseAPI .= '",';
				}
				$responseAPI .= '"Task Name" : "';
				$responseAPI .= $taskName;
				$responseAPI .= '",';
				$responseAPI .= '"Points Possible" : "';
				$responseAPI .= $points;
				$responseAPI .= '",';
				$responseAPI .= '"Due Date" : "';
				$responseAPI .= $dueDate;
				$responseAPI .= '"},';
				
			}
			$responseAPI = rtrim($responseAPI, ", ");
			$responseAPI .= ']},';


			array_push($classNames, $class['name']);
		}
		$responseAPI = rtrim($responseAPI, ", ");
		$responseAPI .= ']}';
		echo $responseAPI;

		
	}

	if($_GET['url'] == 'orderTasks'){
		$orderedTasks = array();
		$all_tasks = $users->query("SELECT * FROM tasks WHERE userid = $result[id] AND timeLength > 0");
		if(mysqli_num_rows($all_tasks) == 0){
			return;
		}
		$today = strtotime(date("Y-m-d"));
		while($oneTask = $all_tasks->fetch_assoc()){
			$totnum = $oneTask['timeLength'] / $oneTask['priority'];
			$tillDueDate = strtotime($oneTask['dueDate']);
			$datediff = $tillDueDate - $today;
			$tillDueDate = round($datediff / (60 * 60 * 24));
			if($tillDueDate == 0){
				$totnum = 999;
			}
			else{
				$totnum /= $tillDueDate;
			}
			array_push($orderedTasks, array($oneTask['id'], $totnum));
		}
		for($i = 0; $i < count($orderedTasks); $i++) {
			$max = $orderedTasks[$i][1];
			$maxIndex = $i;
			for($j = $i; $j < count($orderedTasks); $j++){
				if($orderedTasks[$j][1] > $max){
					$max = $orderedTasks[$j][1];
					$maxIndex = $j;
				}
			}
			$temp = $orderedTasks[$maxIndex];
			$orderedTasks[$maxIndex] = $orderedTasks[$i];
			$orderedTasks[$i] = $temp;
		}

		for($i = 1; $i <= count($orderedTasks); $i++){
			$id = $orderedTasks[$i-1][0];
			$users->query("UPDATE tasks set ordered = $i WHERE id = $id");
		}
	}

	if($_GET['url'] == 'taskInfo'){
		$oneTask = $users->query("SELECT * FROM tasks WHERE userid = $result[id] AND id = $_GET[id]");
		if(mysqli_num_rows($oneTask) == 0){
			http_response_code(405);
			exit();
		}
		$task = mysqli_fetch_assoc($oneTask);
		$response = '{"Tasks":[{';
		$response = $response.'"Task Name": "'.$task['taskName'].'",';
		$response = $response.'"Due Date": "'.$task['dueDate'].'",';
		$response = $response.'"Time Left": '.$task['timeLength'].',';
		$response = $response.'"Priority": '.$task['priority'].'';
		if(strlen($task['description']) == 0){
		}
		else{
			$response = $response.',"Description": "'.$task['description'].'"';
		}
		$response .= "}]}";
		echo $response;
	}
	http_response_code(200);
}

if($requestType == "POST"){

	if($_GET['url'] == 'addSchool'){	
		$name = $_POST['schoolName'];

		if(isset($_POST['yearStart'])){
			$yearStart = $_POST['yearStart'];
		}
		else{
			$yearStart = "NULL";
		}
		if(isset($_POST['yearEnd'])){
			$yearEnd = $_POST['yearEnd'];
		}
		else{
			$yearEnd = "NULL";
		}
		if(isset($_POST['canvasURL'])){
			$canvasURL = $_POST['canvasURL'];
		}
		else{
			$canvasURL = "NULL";
		}
		if(isset($_POST['canvasAPI'])){
			$canvasAPI = $_POST['canvasAPI'];

		}
		else{
			$canvasAPI = "NULL";
		}

		$stmt = $users->prepare("INSERT INTO `user_school` (`userid`, `schoolName`, `startYear`, `endYear`, `canvasURL`, `canvasAPI`) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("isiiss", $result['id'], $name, $yearStart, $yearEnd, $canvasURL, $canvasAPI);

		$check = $stmt->execute();
		if(!$check){
			http_response_code(405);
			exit();
		}

	}

	if($_GET['url'] == 'addTask'){
		$taskName = $_POST['taskName'];
		$sqlDate = explode("/",$_POST['dueDate']);
		$dueDate = $sqlDate[2].'-'.$sqlDate[0].'-'.$sqlDate[1];
		$taskLength = $_POST['completionTime'];
		$priority = $_POST['priority'];
		$description = $_POST['description'];
		if(strlen($description) == 0){
			$description = "";
		}
		
		insert_Task($taskName, $dueDate, $taskLength, $priority, $description, $result['id']);

	}

	if($_GET['url'] == 'editTask'){
		$taskName = $_POST['taskName'];
		$taskId = $_POST['taskId'];
		$dueDate = $_POST['dueDate'];
		$taskLength = $_POST['completionTime'];
		$priority = $_POST['priority'];
		$description = $_POST['description'];
		if(strlen($description) == 0){
			$description = "";
		}
		
		update_Task($taskName, $dueDate, $taskLength, $priority, $description, $taskId);

	}

	if($_GET['url'] == 'addFromCanvas'){
		foreach ($_POST as $key => $value) {
			if(strpos($key, 'taskName') !== false){
				$taskName = $value;
			}
			if(strpos($key, 'dueDate') !== false){
				$dueDate = $value;
			}
			if(strpos($key, 'completionTime') !== false){
				$completionTime = $value;
			}
			if(strpos($key, 'priority') !== false){
				$priority = $value;
			}
			if(strpos($key, 'description') !== false){
				$description = $value;
			}
			if(strpos($key, 'delim') !== false){
				insert_Task($taskName, $dueDate, $completionTime, $priority, $description, $result['id']);
				$taskName = "";
				$dueDate = "";
				$completionTime = "";
				$priority = "";
				$description = "";
			}

		}

	}



	http_response_code(200);
}

function insert_Task($taskName, $dueDate, $taskLength, $priority, $description, $userID){
	$users = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
	$stmt = $users->prepare("INSERT INTO `tasks` (`taskName`, `dueDate`, `timeLength`, `priority`, `description`, `userid`) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssiisi", $taskName, $dueDate, $taskLength, $priority, $description, $userID);
		$check = $stmt->execute();
		if(!$check){
			http_response_code(405);
			exit();
		}
	$stmt->close();
}

function update_Task($taskName, $dueDate, $taskLength, $priority, $description, $taskId){
	$users = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
	$stmt = $users->prepare("UPDATE `tasks` SET `taskName` = ?, `dueDate` = ?, `timeLength` = ?, `priority` = ?, `description` = ? WHERE id = ?");
		$stmt->bind_param("ssiisi", $taskName, $dueDate, $taskLength, $priority, $description, $taskId);
		$check = $stmt->execute();
		if(!$check){
			http_response_code(405);
			exit();
		}
	$stmt->close();
}
