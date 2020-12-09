<?php
session_start();
include('checkCookie.php');
if(!isset($_SESSION['id'])){
    header("Location: registration.php");
}
require('config.php');
$tasks = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Task Manager</title>
    <script src="assets/js/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/User-Activity-Panel.css">
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>

</head>

<body style="background: rgb(1,1,1);color: #1f1b24;">
    <h1 id="dateTime" style="color: #9f9999;"></h1>
    <p class="bounce animated" style="color: rgb(108,169,230);font-size: 20px;">Welcome <?php echo $_SESSION['username']?></p>
    <hr>
    <h1 style="color: rgb(138,64,231);text-align: center;">All Tasks <br><small><em>Ordered for minimal stress</em></small></h1>
    
     <!-- Modal will be filled info of task selected to edit -->
    <div id='editTaskInfo'>    
    </div>
    <div id='checkCanvasInfo'>
        
    </div>
    <!-- Carousel -->
    <div class="carousel slide swing animated" data-ride="carousel" id="carousel-1">
        <!-- Tasks will be filled in from api request -->
        <div id='tasks' class="carousel-inner">
            
        </div>
        <div>
            <a class="carousel-control-prev" href="#carousel-1" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon"></span><span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel-1" role="button" data-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <!-- Number of indicators also gets autofilled -->
        <ol class="carousel-indicators" id="carouselIndicator">
            
        </ol>
    </div>
    <!-- End Carousel -->

    <div>
        <a class="btn btn-primary" role="button" data-toggle="modal" href="#addTask">Add Task</a>
        <a class="btn btn-primary" role="button" data-toggle="modal" href="#userProfile">User Profile</a>
    </div>


        <!-- Modal for form to add task -->
        <div class="modal fade" role="dialog" tabindex="-1" id="addTask">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Add New Task</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="api/addTask" id='addFormTask' method="POST">
                            <input type="hidden" id="apiKey" name="apiKey" value=<?php echo '"'.$_SESSION['apiKey'].'"' ?>>
                            <label class="required" for="taskName">Task Name</label>
                            <input class="form-control" type="text" id="addTaskName" name="taskName" required="">
                            <label class="required" for="dueDate">Due Date</label>
                            <input id="dueDate" class="form-control" type="text" name="dueDate" required>
                            <label class="required" for="completionTime">Completion Time (hours)</label>
                            <div class="range-wrap">
                                <input id="lengthHours" class="form-control-range range" type="range" name="completionTime" min="1" max="15" step="1">
                                <output class="bubble"></output>
                            </div>
                            <label class="required" for="priority">Priority</label>
                            <div class="range-wrap">
                                <input id="priority" class="form-control-range range" type="range" name="priority" min="1" max="10" required="">
                                <output class="bubble"></output>
                            </div>
                            <label for="description">Description<br>
                            <textarea class="form-control" name="description"></textarea>
                            </label>
                    </div>
                    <div id="addFooter" class="modal-footer">
                        <button class="btn btn-light" data-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary" id="submitButtonId">Save</button>
                        <button class="btn btn-primary" onclick="addCanvas()" ondata-dismiss="modal" type="button">Add From Canvas</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->

        <!-- User Profile Modal -->
        <div class="modal fade" role="dialog" tabindex="-1" id="userProfile">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>User Profile</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div>
                                        <img class="mr-3" style="height: 50px; width: 50px;" src="assets/img/generic-avatar.png">
                                    </div>
                                    <div class="media-body">
                                        <ul class="list-unstyled fa-ul">
                                            <li><i class="fa fa-user fa-li"></i><a href="#"><?php echo $_SESSION['username'] ?></a></li>
                                            <li id="tasksRemaining"></li>
                                            <li id="hoursLeft"></li>
                           
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div>
                                    <small>
                                        <strong>Stress Level</strong><i class="fa fa-info-circle text-primary"></i>
                                    </small>
                                    <div class="progress progress-high">
                                        <div class="progress-bar bg-danger" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%;"><span class="sr-only">90%</span></div>
                                    </div>
                                </div>


                                <div id='schoolSetUp'>

                                </div>

                                <div>
                                    <a class="btn btn-link btn-sm text-uppercase btn-text" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-1" href="#collapse-1" role="button">
                                        <strong>Additional Information </strong><i class="fa fa-chevron-down fa-fw"></i>
                                    </a>
                                    <div class="collapse" id="collapse-1">
                                        <div class="media">
                                            <div>
                                                <span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i>
                                                    <i class="fa fa-area-chart fa-stack-1x fa-inverse" aria-hidden="true"></i>
                                         </span>
                                            </div>
                                            <div class="media-body">
                                                <p>
                                                    <strong>Statistics</strong><br><small>
                                                        <strong>Total Tasks Completed</strong> <em>48</em><br>
                                                        <strong>Total Hours Worked:</strong>32 Hours</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Close</button></div>
            </div>
        </div>

    <!-- Modal to show images to help user link Canvas to site -->
    <div id="help" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="assets/img/help1.png" class="img-responsive">
                    <img src="assets/img/help2.png" class="img-responsive">
                </div>
            </div>
        </div>  
    </div>





</body>

<script src="assets/js/clock.js"></script>
<script src="assets/js/bubble.js"></script>
<?php include('assets/js/editTask.js'); ?>

<script>

function loadTasks(){
    $.ajax({ url: "api/getTasks",
        type:'get',
        data:{apiKey:<?php echo "'$_SESSION[apiKey]'" ?>},
        success: function(data){
           var json = $.parseJSON(data); 
           document.getElementById("tasks").innerHTML = json.HTML;
           document.getElementById("carouselIndicator").innerHTML = json.Carasoul;
    }});
}

function getUserInfo(){
    $.ajax({ url: "api/getUserInfo",
        type:'get',
        data:{apiKey:<?php echo "'$_SESSION[apiKey]'" ?>},
        success: function(data){
           var json = $.parseJSON(data);
           if(Object.keys(json['School Information']).length === 0){
                document.getElementById("schoolSetUp").innerHTML ='<div><a class="btn btn-link btn-sm text-uppercase btn-text" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-2" href="#collapse-2" role="button"><strong>Link To Canvas </strong><i class="fa fa-chevron-down fa-fw"></i></a><div class="collapse" id="collapse-2"><form id="addFormSchool"><input type="hidden" id="apiKey" name="apiKey" value='+ <?php echo '"'.$_SESSION['apiKey'].'"' ?>+'><input type="hidden" id="cookie" name="cookie" value='+ <?php echo '"'.$_COOKIE['login'].'"' ?>+'><label class="required" for="schoolName">School Name</label><input class="form-control" type="text" id="schoolName" name="schoolName" required><label for="yearStart">Year Start</label><input class="form-control" type="number" id="yearStart" name="yearStart"><label for="yearEnd">Year End</label><input class="form-control" type="number" id="yearEnd" name="yearEnd"><label class="required" for="canvasURL">Canvas URL</label><input class="form-control" type="text" id="canvasURL" name="canvasURL" placeholder="http://colostate.instructure.com" required><label class="required" for="canvasAPI">Canvas API Key</label><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#help">Help</button><input class="form-control" type="text" id="canvasAPI" name="canvasAPI" placeholder="3716~xxx..." required><div class="modal-footer"><button class="btn btn-primary" onclick="addSchool()" id="submitButtonId2">Save</button></div></form></div></div>';

           }
           else{
                var schoolName = json['School Information']['School Name'];
                if('Start Year' in json['School Information']){
                    var year = json['School Information']['Start Year'] + '-' + json['School Information']['End Year'];
                }
                else{
                    var year = "";
                }
                document.getElementById("schoolSetUp").innerHTML = '<h6 class="text-uppercase"><strong>Attending: </strong></h6><div class="media"><div><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-graduation-cap fa-stack-1x fa-inverse" aria-hidden="true"></i></span></div><div class="media-body"><p><strong>'+schoolName+'</strong><br><small><em>'+year+'</em><br></small></p></div></div>';
                
           }
           document.getElementById("tasksRemaining").innerHTML = "<i class='fa fa-file fa-li'></i>" + json.Data['Tasks Remaining'] + " Tasks Remaining";
           document.getElementById("hoursLeft").innerHTML = "<i class='fa fa-hourglass-half fa-li' aria-hidden='true'></i>" + json.Data['Hours Remaining'] + " Hours Scheduled";
                           
    }});
}



$(document).ready(function(){
    reOrderTasks();
    getUserInfo();
    
});


$( function() {
    $( "#dueDate" ).datepicker();
} );


$("#submitButtonId").click(function() {
    var url = "api/addTask";
  
    $.ajax({
           type: "POST",
           url: url,
           data: $("#addFormTask").serialize(), // serializes the form's elements.
           success: function(data)
           {
                reOrderTasks();
                $('#addTask').modal('hide');

           }
         });

    return false; // avoid to execute the actual submit of the form.
});

function submitEditTask(){
    $("#editFormTask").submit(function(e){
        e.preventDefault();
    });
    var url = "api/editTask";
  
    $.ajax({
           type: "POST",
           url: url,
           data: $("#editFormTask").serialize(), // serializes the form's elements.
           success: function(data)
           {
                reOrderTasks();
                $('#editTask').modal('hide');
                getUserInfo();

           }
         });
}

function addFromCanvas(){
    $("#editFormCanvas").submit(function(e){
        e.preventDefault();
    });
    var url = "api/addFromCanvas";
  
    $.ajax({
           type: "POST",
           url: url,
           data: $("#editFormCanvas").serialize(), // serializes the form's elements.
           success: function(data)
           {
                reOrderTasks();
                $('#addCanvas').modal('hide');
                getUserInfo();

           }
         });
}



function addSchool(){
    $("#addFormSchool").submit(function(e){
        e.preventDefault();
    });
    var url = "api/addSchool";
  
    $.ajax({
           type: "POST",
           url: url,
           data: $("#addFormSchool").serialize(), // serializes the form's elements.
           success: function(data)
           {

                $('#userProfile').modal('hide');
                getUserInfo();

           }
         });
}

function addCanvas(){
    var url = "api/getCanvas";
    document.getElementById("addFooter").innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';
  
    $.ajax({
           type: "GET",
           url: url,
           data: {apiKey: <?php echo "'$_SESSION[apiKey]'"?>, "Canvas URL" : <?php echo "'$_SESSION[canvasURL]'" ?>, "API Key" : <?php echo "'$_SESSION[canvasAPI]'" ?> },
           success: function(data)
           {
                var apiKey = <?php echo "'$_SESSION[apiKey]'"?>;
                var json = $.parseJSON(data);
                var header = '<div class="modal fade" role="dialog" tabindex="-1" id="addCanvas"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4>Verify Tasks</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div><div class="modal-body"><form action="api/addManyTasks" id="editFormCanvas" method="POST"><input type="hidden" id="apiKey" name="apiKey" value="'+apiKey+'"">';
                var footer = '<div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Close</button><button class="btn btn-primary" onclick="addFromCanvas()" id="submitButtonId3">Save</button></div></form></div></div></div>';
                var body = "";
                var button = '<a class="btn btn-primary" style="display:none" role="button" id="clickuy2" data-toggle="modal" href="#addCanvas">User Profile</a>';
               
                var counter = 0;

                for(var i = 0; i < Object.keys(json['Classes']).length; i++){
                    var key = Object.keys(json['Classes'][i])[0];
                    for(var j = 0; j < Object.keys(json['Classes'][i][key]).length; j++){
                        counter = counter + 1;
                        var simple = json['Classes'][i][key][j];
                        body += returnModal(simple['Task Name'],simple['Due Date'], 1, 5, simple['Description'], counter);

                    }
                }

                document.getElementById("addFooter").innerHTML = '<button class="btn btn-light" data-dismiss="modal" type="button">Close</button><button class="btn btn-primary" id="submitButtonId">Save</button><button class="btn btn-primary" onclick="addCanvas()" ondata-dismiss="modal" type="button">Add From Canvas</button>';

                document.getElementById('checkCanvasInfo').innerHTML = button + header + body + footer;
                $('#addTask').modal('hide');
                $("#clickuy2").click();
           }
         });

}

function returnModal(taskName, dueDate, completionTime, priority, description="", counter){
    return '<label class="required" for="taskName'+counter+'">Task Name</label><input class="form-control" value="'+taskName+'" type="text" id="editTaskName" name="taskName'+counter+'" required><label class="required" for="dueDate'+counter+'">Due Date</label><input id="editdueDate" value="'+dueDate+'" class="form-control" type="text" name="dueDate'+counter+'" required><label class="required" for="completionTime'+counter+'">Completion Time (hours)</label><input id="editlengthHours" value="'+completionTime+'" class="form-control-number" type="number" name="completionTime'+counter+'" min="0" max="15"><br><label class="required" for="priority'+counter+'">Priority</label><input id="editpriority" value="'+priority+'" class="form-control-number number" type="number" name="priority'+counter+'" min="1" max="10" required><br><label for="description'+counter+'">Description<br><textarea id="editDescription" class="form-control" name="description'+counter+'">'+description+'</textarea></label><input type="hidden" id="delim" name="delim'+counter+'"><br>';
}

function reOrderTasks(){
    var url = "api/orderTasks";
  
    $.ajax({
           type: "GET",
           url: url,
           data: {apiKey: <?php echo "'$_SESSION[apiKey]'"?> },
           success: function(data)
           {
            loadTasks();
           }
         });
}

</script>


</html>