 <script>
 function editTask(taskid){
     var url = "api/taskInfo";
  
    $.ajax({
           type: "GET",
           url: url,
           data: {apiKey: <?php echo "'$_SESSION[apiKey]'"?> },
           success: function(data)
           {
            alert(data);
           }
         });
    var test2 = '<a class="btn btn-primary" style="display:none" role="button" id="clickuy" data-toggle="modal" href="#editTask">User Profile</a>';

    var test = '<div class="modal fade" role="dialog" tabindex="-1" id="editTask"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4>Edit Tasks</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div><div class="modal-body"><form action="api/editTask" id="editFormTask" method="POST"><input type="hidden" id="apiKey" name="apiKey" value="'+apiKey+'"><label class="required" for="taskName">Task Name</label><input class="form-control" value="'+taskName+'" type="text" id="editTaskName" name="taskName" required><label class="required" for="dueDate">Due Date</label><input id="editdueDate" value="'+dueDate+'" class="form-control" type="text" name="dueDate" required><label class="required" for="completionTime">Completion Time (hours)</label><input id="editlengthHours" value="'+completionTime+'" class="form-control-number" type="number" name="completionTime" min="0" max="15"><br><label class="required" for="priority">Priority</label><input id="editpriority" value="'+priority+'" class="form-control-number number" type="number" name="priority" min="1" max="10" required><br><label for="description">Description<br><textarea id="editDescription" value="'+description+'" class="form-control" name="description"></textarea></label></div><div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Close</button><button class="btn btn-primary" id="submitButtonId3">Save</button></div></form></div></div></div>';
    document.getElementById("editTaskInfo").innerHTML = test2 + test;
    $("#clickuy").click();

}
</script>
