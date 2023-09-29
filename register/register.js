function sendRegisterRequest() {

    const studentData = new FormData(document.getElementById('studentModal-form'));

    //get form values
    let email = document.getElementById("register-form").elements["email"].value;
    let password = document.getElementById("register-form").elements["password"].value;
    let firstname = document.getElementById("register-form").elements["firstname"].value;
    let lastname = document.getElementById("register-form").elements["lastname"].value;
    let id = document.getElementById("register-form").elements["student_id"].value;
    let student = document.getElementById("register-form").elements["student"].checked;
    let TA = document.getElementById("register-form").elements["TA"].checked;
    let prof = document.getElementById("register-form").elements["professor"].checked;
    let admin = document.getElementById("register-form").elements["admin"].checked;
    let sysop = document.getElementById("register-form").elements["sysop"].checked;


    try {
        //send request
        const syncRequest = new XMLHttpRequest();
        syncRequest.open("POST", "register.php", false);
        syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        syncRequest.send(`email=${email}&password=${password}&firstname=${firstname}&lastname=${lastname}&student=${student}&ta=${TA}&professor=${prof}&sysop=${sysop}&admin=${admin}&studentCourses=${studentData.getAll('studentCourses')}&student_id=${id}`);

        if (syncRequest.status === 200) {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/xml");
            let scripts = xmlDoc.getElementsByTagName("script");

            // register success
            if (scripts.length > 0) {
                document.body.innerHTML = syncRequest.responseText;
                let scripts = document.body.getElementsByTagName("script");
                eval(scripts[0].text); // execute the declaration code for our returned 
                // functions so that the browser knows they exist
                redirect(); // redirect to the user's dashboard
            }
            // login fail
            else {
                let errorDiv = document.getElementById("register-error");
                errorDiv.innerHTML = syncRequest.responseText;
                modalFadeOut('studentModal');   

            }
        }
        
    }
    catch (exception) {
        alert("Request failed. Please try again.");
    }
}
//get list of existing courses in the modal
function getCourses(){
    try {
        const req = new XMLHttpRequest();
        req.open("GET", `../register/get_courses.php`, true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              let div = document.getElementById("studentcourses");
              div.innerHTML = req.responseText.replaceAll('ID_TO_REPLACE','studentCourses');      
            }
        }
        req.send();
      } catch (exception) {
        alert("Request failed. Please try again.");
      }
}

//open the modal or send the request
function regButton(){
    let student = document.getElementById("register-form").elements["student"].checked;
    let email = document.getElementById("register-form").elements["email"].value;
    let password = document.getElementById("register-form").elements["password"].value;
    let firstname = document.getElementById("register-form").elements["firstname"].value;
    let lastname = document.getElementById("register-form").elements["lastname"].value;
    if (email=="" || password=="" || firstname==""||lastname==""){
        document.getElementById("register-error").innerHTML = "Please fill in your name, email and password"
    }

    else if (student){
        animateModal('studentModal');
    }else{
        sendRegisterRequest();
    }
}



function animateModal(modalName){
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
    span.onclick = function (){modalFadeOut(modalName);}

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modalFadeOut(modalName);
        }
    }
}

function modalFadeOut(modalName){
    var modal = document.getElementById(modalName);
    //animation
    modal.style.animationName = "fadeOut_Modal";
    modal.style.animationFillMode = "forwards";
    document.body.style.overflow = "auto";
    //1.2s long animation so display=none after
    setTimeout(() => {
        modal.style.display = "none";
    }, 500);
}

function showID(){
    let student = document.getElementById("register-form").elements["student"].checked;
    let TA = document.getElementById("register-form").elements["TA"].checked;   
    var input = document.getElementById("student_id");
    if(student || TA)
    input.style.display = "block";
    else
    input.style.display = "none";
 
}