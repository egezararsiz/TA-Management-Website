function populate(){
    let course = document.getElementById("selectCourse").value
    const courseArray = course.split(" - ");
    course = courseArray[0];
    let semester = courseArray[2].split(" ");
    if(!course)
      return;
      //get the right course in the chat
    document.getElementById("chatFrame").src =`../ta/chat.php?course=${course}&term=${semester[0]}&year=${semester[1]}`
    try {
        //fill the ta sheet
        const req = new XMLHttpRequest();
        req.open("GET", `../ta/get_sheet.php?course=${course}&term=${semester[0]}&year=${semester[1]}`, true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              let div = document.getElementById("sheet");
              div.innerHTML = req.responseText;
            }
        }
        req.send();
      } catch (exception) {
        alert("Request failed. Please try again.");
      }

      try {
        //fill the announcements
        const req = new XMLHttpRequest();
        req.open("GET", `../ta/get_announcements.php?course=${course}&term=${semester[0]}&year=${semester[1]}`, true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              let div = document.getElementById("nav-announcements");
              div.innerHTML = req.responseText;
            }
        }
        req.send();
      } catch (exception) {
        alert("Request failed. Please try again.");
      }
    }

    //send announcement and refresh page
function sendAnnouncement(){
  const formData = new FormData(document.getElementById("announcement-form"));
  let text = formData.get('content');
  text=text.replaceAll('\n','<br/>');
  try {
    const syncRequest = new XMLHttpRequest();
    syncRequest.open("POST", "../ta/send_announcement.php", false);
    syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    let course = document.getElementById("selectCourse").value
    const courseArray = course.split(" - ");
    course = courseArray[0];
    let semester = courseArray[2].split(" ");
    syncRequest.send(`title=${formData.get('title')}&message=${text}&course=${course}&term=${semester[0]}&year=${semester[1]}`);

    if (syncRequest.status == 200) {
              // check if we received an error while trying to register
              let parser = new DOMParser();
              let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/xml");
              let error_msgs = xmlDoc.getElementsByClassName("error");
        if (error_msgs.length > 0) {
          let error_div = document.getElementById("error-msg-cont");
          // append all error messages
          for (msg of error_msgs) {
              error_div.appendChild(msg);
          }
       }
        populate();
        modalFadeOutSheet('announcementModal');
    }
} catch (exception) {
    console.log(exception);
    alert("Request failed. Please try again.");
}

}

//update ta sheet and refresh
function updateTASheet(toUpdate){
  const formData = new FormData(document.getElementById(toUpdate+"-form"));
  try {
    const syncRequest = new XMLHttpRequest();
    syncRequest.open("POST", "../ta/update_sheet.php", false);
    syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    let course = document.getElementById("selectCourse").value
    const courseArray = course.split(" - ");
    course = courseArray[0];
    let semester = courseArray[2].split(" ");
    syncRequest.send(`toChange=${formData.get('toUpdate')}&new=${formData.get('new')}&course=${course}&term=${semester[0]}&year=${semester[1]}&email=${formData.get('email')}`);

    if (syncRequest.status == 200) {
        let parser = new DOMParser();
        let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/xml");
        let error_msgs = xmlDoc.getElementsByClassName("error");
        
        // check if we received an error while trying to register
        if (error_msgs.length > 0) {
            let error_div = document.getElementById("error-msg-cont");
            // append all error messages
            for (msg of error_msgs) {
                error_div.appendChild(msg);
            }
        }
        populate();
        modalFadeOutSheet(toUpdate);
    }
} catch (exception) {
    console.log(exception);
    alert("Request failed. Please try again.");
}

}


//show a modal with the right name
function showModal(name,taName,taEmail){
  let mailInput = document.getElementById(name+'-email');
  mailInput.value = taEmail;
  let title = document.getElementById(name+'-head');
  title.innerHTML = "Change "+taName+ "'s "+name;
  animateModalSheet(name);
}





function animateModalSheet(modalName){
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
  span.onclick = function (){modalFadeOutSheet(modalName);}

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
      if (event.target == modal) {
        modalFadeOutSheet(modalName);
      }
  }
}

function modalFadeOutSheet(modalName){
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