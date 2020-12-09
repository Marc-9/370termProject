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