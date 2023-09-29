function sendRating(taid,course) {


    let rating = document.getElementById(taid+course).elements["rating"].value;
    let comment = document.getElementById(taid+course).elements["comment"].value;
    const courseArray = course.split(" - ");
    coursename = courseArray[0];
    let semester = courseArray[1].split(" ");
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../student/send_rating.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`taID=${taid}&course=${coursename}&comment=${comment}&term=${semester[0]}&year=${semester[1]}&rating=${rating}`);
        document.getElementById(taid+course+"-err").innerHTML = "Your rating was saved.";
    }
    catch (exception) {
        alert(exception);
    }
}