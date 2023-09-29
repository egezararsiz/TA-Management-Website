//helper
function populateTATable(request) {
    let table = document.getElementById("tas-table");
    table.innerHTML = request.responseText;
}

//get TAs
function getTAs() {
    try {
        const req = new XMLHttpRequest();
        req.open("GET", "../admin/get_tas.php", true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                populateTATable(req);
            }
        }
        req.send(null);
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

function saveNewTA() {
    // Clear error messages
    const error_div = document.getElementById("addTAModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    const formData = new FormData(document.getElementById("add-ta-form"));
    //send form
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../admin/add_tas.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=admin&ta=${formData.get('ta-email')}&term=${formData.get('semester')}&year=${formData.get('year')}&hours=${formData.get('hours')}`);

        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
            // check for error
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            //success
            else{
                let success_msg = xmlDoc.getElementsByClassName("success");
                for (msg of success_msg) {
                    error_div.appendChild(msg);
                }
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

//the table when clicked on pencil button and correctly searched for a TA to remove/add hours
function displayTA(){
    //clear error messages
    const error_div = document.getElementById("editTAModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    const formData = new FormData(document.getElementById("edit-ta-form"));
    //send form
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../admin/display_ta.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=admin&ta=${formData.get('ta-email')}&term=${formData.get('semester')}&year=${formData.get('year')}`);
        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
            // check if we received an error 
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            else{
                //show display:none divs when correctly searched for a TA
                let table = document.getElementById("importantTAinfo");
                table.innerHTML = syncRequest.responseText;
                let form = document.getElementById("assign-removeTA");
                form.style.display = "block";
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }

}

//assigning hours
function assignTA(){
    //clear errors
    const error_div = document.getElementById("editTAModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //send form
    const formData = new FormData(document.getElementById("assign-ta-form"));
    var email = document.getElementById("ta-table-email").innerHTML;
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../admin/assign_ta.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=admin&ta=${email}&courseNumber=${formData.get("ta-course")}&term=${formData.get("semester")}&year=${formData.get("year")}&assign=${formData.get("assignTA")}&remove=${formData.get("removeTA")}&hours=${formData.get("ta-hours")}`);
       
        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
            // check if we received an error
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            //if successful
            else{
                let success_msg = xmlDoc.getElementsByClassName("success");
                for (msg of success_msg) {
                    error_div.appendChild(msg);
                }
            }
        }
        else{
            console.log(syncRequest.responseText);
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
    
}

function animateTAModal(modalName){
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
        modalTAFadeOut(modalName);
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modalTAFadeOut(modalName);
        }
    }
}

function modalTAFadeOut(modalName){
    var modal = document.getElementById(modalName);
    //animation
    modal.style.animationName = "fadeOut_Modal";
    modal.style.animationFillMode = "forwards";
    document.body.style.overflow = "auto";
    //0.5s long animation so display=none after
    setTimeout(() => {
        modal.style.display = "none";
    }, 500);
    getTAs();
    //reset forms and hide some divs
    document.getElementById("add-ta-form").reset();
    document.getElementById("edit-ta-form").reset(); 
    document.getElementById("importantTAinfo").innerHTML = "";  
    document.getElementById("assign-removeTA").style.display = "none";  
}