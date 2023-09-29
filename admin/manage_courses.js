//helpr
function populateCourseTable(request) {
    let table = document.getElementById("course-table");
    table.innerHTML = request.responseText;
}

//get courses
function getTACourses() {
    try {
        const req = new XMLHttpRequest();
        req.open("GET", "../admin/get_courses.php", true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                populateCourseTable(req);
            }
        }
        req.send(null);
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}