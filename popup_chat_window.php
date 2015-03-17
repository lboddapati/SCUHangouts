<?php
    session_start();
    $username = $_GET['username'];
    // Check if user is logged in. Otherwise redirect to login page.
    if(!isset($_SESSION[$username])) {
        header("Location: index.php");
    }
    $friend = $_GET['friend'];
?>

<html>
<head>
    <title>Chatting with <?php echo $friend ?></title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/popup_chat_window.css">
</head>
<body>
    <div id="chat"></div>
    <textarea name="message" id="message_box" wrap="hard"></textarea>

    <script type="text/javascript">
        $(document).ready(function(){
            //console.log("ready");
            get_chat_history();
            $('#chat').animate({"scrollTop": $('#chat')[0].scrollHeight}, "fast");
        });

        // function to get chat history from the database.
        function get_chat_history() {
            $.ajax({
                url: 'get_chat_history.php',
                data: "user=<?php echo $username ?>&friend=<?php echo $friend ?>",
                success: function(data) {
                    $("#chat").empty();
                    $("#chat").append(data);
                }
            });
            //$('#chat').scrollTop = $('#chat').scrollHeight;
            setTimeout(get_chat_history, 1000);  //refresh every 1 sec.
        }

        // send message to friend and store in server database.
        $("#message_box").keyup(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){    //Check if enter key pressed.
                var message = $("#message_box").val().trim();

                if (message.replace(/\s+/g, '') != '') {
                    //var msg = $("<p>", {class:"sent"});
                    var d = new Date();
                    var t = d.toLocaleDateString()+" "+d.toLocaleTimeString().replace('PST','').trim();
                    var msgString = "<span class='time'>("+t+")</span> <?php echo $username?>:<br>"+message;
                    //msg.html(msgString);
                    //$("#chat").append(msg);
                    $("#message_box").val('');

                    $.ajax({
                        type: 'POST',
                        url: 'save_chat_message.php',
                        data: "sender=<?php echo $username ?>&receiver=<?php echo $friend ?>&timestamp="+t+"&msg="+message,
                        success: function(data){
                            //alert(data);
                        }
                    });

                    //$('#chat').animate({"scrollTop": $('#chat')[0].scrollHeight}, "fast");
                }
            }
        });
    </script>
</body>
</html>
