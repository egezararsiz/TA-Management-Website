//helper
function populateUserTable(request) {
    let table = document.getElementById("user-table");
    table.innerHTML = request.responseText;
}

//get user accs and display
function getAccounts() {
    try {
        const req = new XMLHttpRequest();
        req.open("GET", "../sysop/get_users.php", true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                populateUserTable(req);
            }
        }
        req.send(null);
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

//for user file imports
function saveMultipleNewAccounts() {
    //clear error div
    const error_div = document.getElementById("importModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //get file
    let csv = document.getElementById("user-upload-csv").files[0];
    let formData = new FormData();
    formData.append("file", csv);


    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/import_users.php", false);
        //send file
        syncRequest.send(formData);
        
        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
            //check for error
            if (error_msgs.length > 0) {
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            //check for success
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

//manual add
function saveNewAccount() {
    // Clear error messages
    const error_div = document.getElementById("addModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //form
    const formData = new FormData(document.getElementById("add-user-form"));
    userRoles = ["student", "professor", "ta", "admin", "sysop"];
    selectedRoles = [];
    //convert to numbers (1 -> 5)
    for (var pair of formData.entries()) {
        if (userRoles.includes(pair[0])) {
            selectedRoles.push(userRoles.indexOf(pair[1])+1);
        }
    }

    try {
        //send to backend
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/add_users.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=sysop&password=${formData.get('pwd')}&firstname=${formData.get('first-name')}&lastname=${formData.get('last-name')}&email=${formData.get('email')}&accounttypes=${JSON.stringify(selectedRoles)}&studentID=${formData.get("sid")}`);
        
        if (syncRequest.status == 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            //check for errors
            if (error_msgs.length > 0) {
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

//remove acct
function removeAccount(){
    let error_div = document.getElementById("removeModal-err");
    const formData = new FormData(document.getElementById("remove-user-form"));

    //remove error msgs
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/remove_users.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //send email
        syncRequest.send(`sender=sysop&email=${formData.get('email')}`);
        if (syncRequest.status == 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
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

function animateModal(modalName){
    // Get the modal
    var modal = document.getElementById(modalName);
    // Get the <span> element that closes the modal
    var span = document.getElementById(modalName + "-close");
    //error div
    let error_div = document.getElementById(modalName + "-err");
    //table div for edit
    let table_div = document.getElementById("editModal-table");

    //if modal is not on the screen
    if (modal.style.display == "none" || modal.style.display == ""){
        //remove previous messages
        while (error_div.firstChild) {
            error_div.removeChild(error_div.lastChild);
        }
        while (table_div.firstChild && modalName === "editModal") {
            table_div.removeChild(table_div.lastChild);
        }
        //animation
        modal.style.animationName = "fadeIn_Modal";
        modal.style.display = "block";
        document.body.style.overflow = "hidden";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function (){
        modalFadeOut(modalName);
        if(modalName === "usertypeModal"){
            window.onclick = function(event){
                let newmodal = document.getElementById("editModal");
                if (event.target == newmodal){
                    modalFadeOut("editModal");
                }
            }
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modalFadeOut(modalName);

            //double animation for usertype edit
            if(modalName === "usertypeModal"){
                window.onclick = function(event){
                    let newmodal = document.getElementById("editModal");
                    if (event.target == newmodal){
                        modalFadeOut("editModal");
                    }
                }
            }
        }
    }
}

function modalFadeOut(modalName){
    var modal = document.getElementById(modalName);
    //animation
    modal.style.animationName = "fadeOut_Modal";
    modal.style.animationFillMode = "forwards";
    document.body.style.overflow = "auto";
    //0.5s long animation so display=none after
    setTimeout(() => {
        modal.style.display = "none";
    }, 500);

    //user accounts refresh
    getAccounts();

    //reset certain fields
    if(modalName !== "usertypeModal" && modalName !== "importModal"){
        document.getElementById(modalName + "-email").value = '';
    }
    if(modalName === "importModal"){
        document.getElementById("user-upload-csv").value = '';
    }

    if(modalName === "addModal"){
        document.getElementById("add-user-form").reset(); 
        document.getElementById("studentID-div").style.display = "none";
    }
}

//modifies edit modal
function editAccount(){
    //clear error msgs
    let error_div = document.getElementById("editModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //table inside modal
    let table_div = document.getElementById("editModal-table");
    const formData = new FormData(document.getElementById("edit-user-form"));

    //send to backend
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/edit_users.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=sysop&email=${formData.get('email')}`);
        if (syncRequest.status == 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            //display table if successful
            else{
                var rows = xmlDoc.getElementsByTagName("table");
                var fulltable = "<table>"+rows[0].innerHTML+"</table>";
                table_div.innerHTML = fulltable;
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

//change text dynamically inside edit modal
function editText(buttonName){
    //for each part of the table, when information is edited
    //that part is also stays up to date
    let button = document.getElementById(buttonName);
    let i_tag = button.getElementsByTagName("i")[0];
    let td_name = buttonName.slice(0,-6) + "td";
    let td = document.getElementById(td_name);

    //red x button for cancel. After cancel, go back to previous state
    let cancel_button = `<button type="button" onclick = "cancelChange(&quot;${td_name}&quot;,&quot;${buttonName}&quot;)" class="btn btn-light" style="margin-right: 5px;">
                        <i class="fa fa-close" style="color:red"></i>
                        </button>`;

    //bit of space
    button.style = "margin-left: 5px;";

    //check button for submitting changes
    i_tag.className ="fa fa-check";
    i_tag.style="color: green;";

    //reset onclick attr. this normally does the animation of pencil to X + check
    button.setAttribute("onClick", "");

    //this is the input in between
    if(td_name.slice(0,5)==="email"){
        td.innerHTML = cancel_button + `<input form="emailForm" name="new_email" type="email"/>`+ td.innerHTML;
    }
    else{
        td.innerHTML = cancel_button + `<input form="${td_name.slice(0,5)}Form" name="new_${td_name.slice(0,5)}" type="text"/>`+ td.innerHTML;
    }
}

//second modal for selecting new user types
function editUserTypes(){

    //clear out error messages
    let error_div = document.getElementById("usertypeModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //form
    const formData = new FormData(document.getElementById("usertypeForm"));
    let allchecked = formData.getAll("usertype[]").join(",");
    let email = document.getElementById("old_email").innerText;

    //send form
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/change_user_usertype.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=sysop&email=${email}&data=${allchecked}`);
        if (syncRequest.status == 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            else{
                //when successful, display msg and update old usertypes
                let success_msgs = xmlDoc.getElementsByClassName("success");
                for (msg of success_msgs) {
                    error_div.appendChild(msg);
                }
                let old_usertype_td = document.getElementById("old_usertype");
                let new_usertype_td = xmlDoc.getElementsByClassName("newusertype")[0];
                old_usertype_td.innerHTML = new_usertype_td.innerHTML;
                document.getElementById("usertypeForm").reset(); 
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
    
}

//for email change
function submitEmailChange(){
    //clear out error msgs
    let error_div = document.getElementById("editModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //form
    const formData = new FormData(document.getElementById("emailForm"));
    let old_email = document.getElementById("old_email").innerText;
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/change_user_email.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //send form
        syncRequest.send(`sender=sysop&old_email=${old_email}&new_email=${formData.get('new_email')}`);
        if (syncRequest.status == 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            else{
                //update email portion of modal, and display success msg
                let success_msgs = xmlDoc.getElementsByClassName("success");
                for (msg of success_msgs) {
                    error_div.appendChild(msg);
                }
                let old_email_td = document.getElementById("old_email");
                old_email_td.innerHTML = `<td id='old_email'>${formData.get('new_email')}</td>`;
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

//first name change
function submitFnameChange(){

    //clear error msg
    let error_div = document.getElementById("editModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }
    const formData = new FormData(document.getElementById("fnameForm"));
    let email = document.getElementById("old_email").innerText;

    //send form
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/change_user_fname.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=sysop&email=${email}&new_fname=${formData.get('new_fname')}`);
        if (syncRequest.status == 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            //again, when successful show the updated fname on screen
            else{
                let success_msgs = xmlDoc.getElementsByClassName("success");
                for (msg of success_msgs) {
                    error_div.appendChild(msg);
                }
                let old_fname_td = document.getElementById("old_fname");
                old_fname_td.innerHTML = `<td id='old_fname'>${formData.get('new_fname')}</td>`;
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

//last name change
function submitLnameChange(){
    //clear error msgs
    let error_div = document.getElementById("editModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }

    //form
    const formData = new FormData(document.getElementById("lnameForm"));
    let email = document.getElementById("old_email").innerText;

    //send form
    try {
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "../sysop/change_user_lname.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`sender=sysop&email=${email}&new_lname=${formData.get('new_lname')}`);
        if (syncRequest.status == 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/html");
            let error_msgs = xmlDoc.getElementsByClassName("error");
            if (error_msgs.length > 0) {
                // append all error messages
                for (msg of error_msgs) {
                    error_div.appendChild(msg);
                }
            }
            else{
                //if successful do what's done above
                let success_msgs = xmlDoc.getElementsByClassName("success");
                for (msg of success_msgs) {
                    error_div.appendChild(msg);
                }
                let old_fname_td = document.getElementById("old_lname");
                old_fname_td.innerHTML = `<td id='old_lname'>${formData.get('new_lname')}</td>`;
            }
        }
    } catch (exception) {
        alert("Request failed. Please try again.");
    }
}

//this is when clicked on x button while editing. go back to previous state
function cancelChange(tdId,buttonName){
    //clear out error msgs
    let error_div = document.getElementById("editModal-err");
    while (error_div.firstChild) {
        error_div.removeChild(error_div.lastChild);
    }
    
    let td = document.getElementById(tdId);
    td.innerHTML = `<button type="submit" form="${buttonName.slice(0,5)}Form" class="btn btn-light" id="${buttonName}" onclick="editText(&quot;${buttonName}&quot;)" style="display: block;margin: auto;" >
                    <i class="fa fa-pencil" style="color:red"></i>
                    </button>`;
    
}

