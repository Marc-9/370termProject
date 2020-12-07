<?php
session_start();
include('checkCookie.php');
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
    <h1 style="color: rgb(138,64,231);text-align: center;">Top 3 Tasks</h1>
    <div class="carousel slide swing animated" data-ride="carousel" id="carousel-1">
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
        <ol class="carousel-indicators">
            <li data-target="#carousel-1" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-1" data-slide-to="1"></li>
            <li data-target="#carousel-1" data-slide-to="2"></li>
        </ol>
    </div>
    <div>
        <a class="btn btn-primary" role="button" data-toggle="modal" href="#addTask">Add Task</a>
        <a class="btn btn-primary" role="button" data-toggle="modal" href="#userProfile">User Profile</a>



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
                    <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary" id="submitButtonId">Save</button></div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" role="dialog" tabindex="-1" id="userProfile">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Modal Title</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div>
                                        <img class="mr-3" style="height: 50px; width: 50px;" src="assets/img/user-photo2.jpg">
                                    </div>
                                    <div class="media-body">
                                        <ul class="list-unstyled fa-ul">
                                            <li><i class="fa fa-user fa-li"></i><a href="#">James Doe</a></li>
                                            <li><i class="fa fa-envelope fa-li"></i><a href="#">james.doe@gmail.com </a></li>
                                            <li><i class="fa fa-phone fa-li"></i>(555) 555-5555</li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div>
                                    <small>
                                        <strong>Propensity to Give </strong><i class="fa fa-info-circle text-primary"></i>
                                    </small>
                                    <div class="progress progress-high">
                                        <div class="progress-bar bg-danger" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%;"><span class="sr-only">90%</span></div>
                                    </div>
                                </div>
                                <h6 class="text-uppercase"><strong>Attending: </strong></h6>
                                <div class="media">
                                    <div>
                                        <span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-ticket fa-stack-1x fa-inverse"></i></span>
                                    </div>
                                    <div class="media-body">
                                        <p>
                                            <a href="#"><strong>Spring Gala </strong><br></a>
                                            <small><em>May 12, 2016</em><br>
                                                <strong>Purchased:</strong> 1 General Admission: $25
                                            </small>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <a class="btn btn-link btn-sm text-uppercase btn-text" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-1" href="#collapse-1" role="button">
                                        <strong>Also Attended </strong><i class="fa fa-chevron-down fa-fw"></i>
                                    </a>
                                    <div class="collapse" id="collapse-1">
                                        <div class="media">
                                            <div>
                                                <span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-ticket fa-stack-1x fa-inverse"></i></span>
                                            </div>
                                            <div class="media-body">
                                                <p>
                                                    <a href="#"><strong>Fall Gala </strong><br></a><small><em>October 12, 2015</em><br><strong>Purchased:</strong> 1 General Admission: $25 </small>
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
    </div>



    <div class="modal fade" role="dialog" tabindex="-1" id="editTask1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Modal Title</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                <div class="modal-body">
                    <p class="text-center text-muted">Description </p>
                </div>
                <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Close</button><button class="btn btn-primary" type="button">Save</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" tabindex="-1" id="editTask2">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Modal Title</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                <div class="modal-body">
                    <p class="text-center text-muted">Description </p>
                </div>
                <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Close</button><button class="btn btn-primary" type="button">Save</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" tabindex="-1" id="editTask3">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Modal Title</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                <div class="modal-body">
                    <p class="text-center text-muted">Description </p>
                </div>
                <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Close</button><button class="btn btn-primary" type="button">Save</button></div>
            </div>
        </div>
    </div>

</body>
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

$( function() {
    $( "#dueDate" ).datepicker();
  } );


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


$("#submitButtonId").click(function() {
    var url = "api/addTask"; // the script where you handle the form input.
  
    $.ajax({
           type: "POST",
           url: url,
           data: $("#addFormTask").serialize(), // serializes the form's elements.
           success: function(data)
           {
               alert(data); // show response from the php script.
           }
         });

    return false; // avoid to execute the actual submit of the form.
});
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

</html>