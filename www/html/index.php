<?php

$requestType = $_SERVER['REQUEST_METHOD'];
if($requestType == "POST"){
	$postBody = file_get_contents("php://input");
	echo $postBody."\n";
	http_response_code(200);
}
else if($requestType == "GET"){
	echo "Hello World\n";
	http_response_code(200);
}
else{
	http_response_code(405);
}

?>
