//helper
function populateProfTable(request) {
    let table = document.getElementById("profs-table");
    table.innerHTML = request.responseText;
}

//get and display prof accounts
function getProfAccounts() {
    try {
        const req = new XMLHttpRequest();
        req.open("GET", "../sysop/get_profs.php", true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                populateProfTable(req);
            }
        }
        req.send(null);
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

//for prof csv import
function saveMultipleProfAccounts() {
    //clear error div
    const error_div = document.getElementById("importProfModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //get the file
    let csv = document.getElementById("prof-upload-csv").files[0];
    let formData = new FormData();
    formData.append("file", csv);

    try {
        //send it to php
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/import_profs.php", false);
        syncRequest.send(formData);

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
            //check success msg
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

function saveProfAccount() {
    // Clear error messages
    const error_div = document.getElementById("addProfModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //form
    const formData = new FormData(document.getElementById("add-prof-form"));

    try {
        //send form
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/add_profs.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=sysop&professor=${formData.get('prof-email')}&faculty=${formData.get('faculty')}&department=${formData.get('department')}&course=${formData.get('course-num')}`);

        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
            // check if we received an error while trying to register
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            //check if we succeeded
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

function animateProfModal(modalName){
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
        modalProfFadeOut(modalName);
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modalProfFadeOut(modalName);
        }
    }
}

function modalProfFadeOut(modalName){
    var modal = document.getElementById(modalName);
    //animation
    modal.style.animationName = "fadeOut_Modal";
    modal.style.animationFillMode = "forwards";
    document.body.style.overflow = "auto";
    //0.5s long animation so display=none after
    setTimeout(() => {
        modal.style.display = "none";
    }, 500);

    //renew profs
    getProfAccounts();

    //reset certain forms
    document.getElementById("upload-prof-form").reset();
    document.getElementById("add-prof-form").reset();

}