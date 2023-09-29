<?php 

require __DIR__.'/../login/verify.php';

session_start();
$email = verify(session_id(),[2,3,4,5]);
if ($email and $_GET['course'] and $_GET['year'] and $_GET['term']) {

//html head
echo "
<html>
<head>
<meta name=\"viewport\" content=\"initial-scale=1.0\">
<link href=\"chat.css\" rel=\"stylesheet\" />

<link
rel='stylesheet'
href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'
/>
<head>";

//print the messages and textbox and button
echo "
<body>
<div id='content' style='min-height:95%;'>

</div>
<form id='form' action='../ta/send_message.php' method='post' onsubmit='send();return false' >
<textarea wrap='soft' id='message' name='message' >
</textarea>
<input type='hidden' id='course' name='course' value='".$_GET['course']."'/>
<input type='hidden' id='year' name='year' value='".$_GET['year']."'/>
<input type='hidden' id='term' name='term' value='".$_GET['term']."'/>

<button type='submit' ><i class='fa fa-paper-plane' aria-hidden='true'></i></button>
</form>
<script>
document.onload = start();

function start(){
    getMessages();
    document.body.scrollTo(0,100000);
   
}
";
//send a message
echo "
function send() {
    let message = document.getElementById('message');
    const formData = new FormData(document.getElementById('form'));
        try {
        const req = new XMLHttpRequest();
        req.open('POST', './send_message.php', true);
        req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                content.innerHTML = req.responseText;

            }
        }
        req.send(`course=".$_GET['course']."&year=".$_GET['year']."&term=".$_GET['term']."&message=\${message.value}`);
    } catch (exception) {
        alert('Request failed. Please try again.');
    }
    message.value='';
    getMessages();
    setTimeout(scrollTo, 50 ,0, 1000000);
}
";
//get new messages every 5 seconds
echo "
function getMessages(){
    let content = document.getElementById('content');
    try {
        const req = new XMLHttpRequest();
        req.open('GET', './get_messages.php?course=".$_GET['course']."&year=".$_GET['year']."&term=".$_GET['term']."', true);
        req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                content.innerHTML = req.responseText;

            }
        }
        req.send(null);
    } catch (exception) {
        alert('Request failed. Please try again.');
    }
    setTimeout(getMessages, 5000);
    
}
</script>
</body>
</html>
";
}
?>