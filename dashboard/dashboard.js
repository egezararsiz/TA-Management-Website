
window.onload = function()
{
    showContent();
    // displayRatingStats();
}

/**
 * Tells user how many ratings hav been submitted through the app
 */
function displayRatingStats()
{
    try{
        const req = new XMLHttpRequest();
        req.open("POST", "ratingStats.php", false);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req.send();
        let response = req.responseText;
        document.getElementById("ratingStats").innerHTML = response;
        
    }catch (exception)
    {
        alert("Request failed.");
    }
}



function showContent()
{
    try{
        select=document.getElementById("selecttype");
        usertype=select.options[select.selectedIndex].text;

        switch (usertype){
            case "Sysop Tasks":
                page="../sysop/dashboard.php";
                break;
            case "Rate a TA":
                page="../student/dashboard.php";
                break;
            case "TA Management":
                page="../ta/dashboard.php";
                break;
            case "TA Admin":
                page="../admin/dashboard.php";
                break;
            default:
                page="../login/welcome.php";
                break;
        }
        const request = new XMLHttpRequest();
        request.open("GET", page, false);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.send();
        if (request.status === 200) {

            let response = request.responseText;
            document.getElementById("personalizedDashboard").innerHTML = response;

            let scripts = document.body.getElementsByTagName("script");
            for(i=0;i<scripts.length;i++){
                eval(scripts[i].text);
            }

            const drop = document.getElementById("myTopnav");
            drop.className = "topnav";

            } else {
                let errorDiv = document.getElementById("login-error");
                errorDiv.innerHTML = syncRequest.responseText;
            }
        
    } catch (exception) {
        alert("Request failed.");
    }
}

function openTab(section,tabName){
    select=document.getElementById("selecttype");
    select.options[select.selectedIndex].text = section;
    showContent();
    tabs = document.getElementsByClassName("nav-item");
    for(let element of tabs){
        if(element.innerHTML == tabName) element.click();
    }
}

function dashboardPage(){
    try{
        
        const request = new XMLHttpRequest();
        request.open("GET", "../login/welcome.php", false);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.send();
        if (request.status === 200) {

            let response = request.responseText;
            document.getElementById("personalizedDashboard").innerHTML = response;

            let scripts = document.body.getElementsByTagName("script");
            for(i=0;i<scripts.length;i++){
                eval(scripts[i].text);
            }

            } else {
                let errorDiv = document.getElementById("login-error");
                errorDiv.innerHTML = syncRequest.responseText;
            }
        
    } catch (exception) {
        alert("Request failed.");
    }
}


function showHamMenu() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}

function selectPage(option){
    select=document.getElementById("selecttype");
    select.value=option;
    select.dispatchEvent(new Event('change'));
}



