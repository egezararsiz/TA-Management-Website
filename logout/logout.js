function sendLogoutRequest() {
    const syncRequest = new XMLHttpRequest();
    syncRequest.open("POST", "../logout/logout.php", false);
    syncRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    syncRequest.send();

    if (syncRequest.status === 200) {
        let parser = new DOMParser();
        let xmlDoc = parser.parseFromString(syncRequest.responseText, "text/xml");
        let scripts = xmlDoc.getElementsByTagName("script");
        // login success
        if (scripts.length > 0) {
            document.body.innerHTML = syncRequest.responseText;
            let scripts = document.body.getElementsByTagName("script");
            eval(scripts[0].text); // execute the declaration code for our returned 
            // functions so that the browser knows they exist
            redirect(); // redirect to the user's dashboard
        }
}}