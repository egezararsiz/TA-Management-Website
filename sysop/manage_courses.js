//helper func
function populateCourseTable(request) {
    let table = document.getElementById("course-table");
    table.innerHTML = request.responseText;
}

//get and display all courses
function getCourses() {
    try {
        const req = new XMLHttpRequest();
        req.open("GET", "../sysop/get_courses.php", true);
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

//for course csv import
function saveMultipleCourses() {
    //clear err messages
    const error_div = document.getElementById("importCourseModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //get and send file
    let csv = document.getElementById("course-upload-csv").files[0];
    let formData = new FormData();
    formData.append("file", csv);
    
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/import_courses.php", false);
        syncRequest.send(formData);

        //if successful
        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            //print if any error messages
            if (error_msgs.length > 0) {
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            else{
            //print if successful
                let success_msgs = xmlDoc.getElementsByClassName("success");
                for (msg of success_msgs) {
                    error_div.appendChild(msg);
                }
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

function saveCourse() {
    // Clear error messages
    const error_div = document.getElementById("addCourseModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    const formData = new FormData(document.getElementById("add-course-form"));
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/add_courses.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //send form
        syncRequest.send(`sender=sysop&courseNumber=${formData.get('course-num')}&courseName=${formData.get('course-name')}&courseDescription=${formData.get('course-desc')}&term=${formData.get('semester')}&year=${formData.get('year')}&instrEmail=${formData.get('course-email')}`);

        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
            //print error/success msgs
            if (error_msgs.length > 0) {
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            else{
                let success_msgs = xmlDoc.getElementsByClassName("success");
                for (msg of success_msgs) {
                    error_div.appendChild(msg);
                }
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}


function editCourse() {
    //clear err messages
    const error_div = document.getElementById("assignCourseModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    const formData = new FormData(document.getElementById("assign-course-form"));
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/edit_courses.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //send form
        syncRequest.send(`sender=sysop&courseNumber=${formData.get('course-num')}&profEmail=${formData.get('prof-email')}&term=${formData.get('semester')}&year=${formData.get('year')}&assign=${formData.get('assign')}&remove=${formData.get('remove')}`);

        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
            //print err/success msg
            if (error_msgs.length > 0) {
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            else{
                let success_msgs = xmlDoc.getElementsByClassName("success");
                for (msg of success_msgs) {
                    error_div.appendChild(msg);
                }
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}


function animateCourseModal(modalName){
    // Get the modal
    var modal = document.getElementById(modalName);
    // Get the <span> element that closes the modal
    var span = document.getElementById(modalName + "-close");
    //error div
    let error_div = document.getElementById(modalName + "-err");

    //if modal is not on the screen
    if (modal.style.display == "none" || modal.style.display == ""){
        //remove previous messages
        while (error_div.firstChild) {
            error_div.removeChild(error_div.lastChild);
        }
        
        //animation
        modal.style.animationName = "fadeIn_Modal";
        modal.style.display = "block";
        document.body.style.overflow = "hidden";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function (){
        modalCourseFadeOut(modalName);
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modalCourseFadeOut(modalName);
        }
    }
}

function modalCourseFadeOut(modalName){
    var modal = document.getElementById(modalName);
    //animation
    modal.style.animationName = "fadeOut_Modal";
    modal.style.animationFillMode = "forwards";
    document.body.style.overflow = "auto";
    //0.5s long animation so display=none after
    setTimeout(() => {
        modal.style.display = "none";
    }, 500);
    
    //refresh courses
    getCourses();
    //reset forms
    document.getElementById("add-course-form").reset();
    document.getElementById("assign-course-form").reset();

}