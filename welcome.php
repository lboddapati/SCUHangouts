<?php
    session_start();
    $username = $_GET['username'];
    if(!isset($_SESSION[$username])) {  // Check that user is logged in properly
        header("Location: index.php");  // If not, redirect to login page
    }
    $pic = $_GET['pic'];
?>
<html>
<head>
    <title>Welcome <?php echo $username?>!</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/welcome.css">
</head>
<body>
    <div id="profile">
            <div id="header">
                <img id="profile_pic" src="<?php echo $pic?>" alt="change" text="change" onclick="popup_upload_profile_pic()"/>
                <h1 id="username"><?php echo $username?></h1>
            </div>
            <img id="logout" class="imageButton" src="icons/logout.png" alt="Logout" title="Logout" onclick="location.href='logout.php?username=<?php echo $username ?>'"/>
            <img id="add_friend" class="imageButton" src="icons/add_friend.png" alt="Add friend" title="Add friend" />
            <form id="popup_edit_profile_pic" class="popup_form" enctype="multipart/form-data" name="popup_edit_profile_pic">
                <input type="file" name="picture" id="new_picture" accept="image/*" required/>
                <input type="text" id="picture_name" placeholder="No file chosen" disabled="disabled">
                <span id="uploadBtnTrigger">Upload Profile Picture</span>
                <input type="image" id="save" class="imageButton" alt="Save" title="Save" src="icons/ok.png"/>
                <input type="image" class="cancel imageButton" alt="Cancel" src="icons/cancel.png">
            </form>
    </div>
    <div id="popup_add_friend" class="popup_form">
        <input type="text" name="friendname" id="friendname" required placeholder="Enter username" required/> <br>
        <input type="image" id="add" class="imageButton" src="icons/add.png" alt="Add" title="Add"/>
        <input type="image" class="cancel imageButton" alt="Cancel" title="Cancel" src="icons/cancel.png" />
    </div>
    <div id="container">
        <div id="friends">
            <h3>Friends</h3>
            <div id="displaylist"></div>
        </div>

        <div id="notifications">
            <h3>Friend Requests</h3>
            <div id="sent_notifications"></div>
            <div id="received_notifications"></div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            display_friends();
            display_received_requests();
            display_sent_requests();
            //TODO: display blocked friends
            //TODO: display notification when new message received
        });



        // function to fetch and display friend list from database.
        function display_friends(){
            $.ajax({
                url: 'get_friendlist.php',
                data: 'username=<?php echo $username?>',
                datatype: "json",
                success: function(data) {
                    var friends_list = $.parseJSON(data);
                    var list = $("<table>", {id:"friendlist"});
                    $.each(friends_list, function(index,friend) {
                        var row = $("<tr>");
                        row.click( function () {popup_chat_window(friend[0])});
                        var pic = $("<td>");
                        var image = $("<img>", {src:friend[2], class:"frnd_profile_pic"})
                        pic.append(image);
                        var name = $("<td>", {class:"name"});
                        name.html(friend[0]);
                        var status = $("<td>", {class:"status"});
                        var indicator = $("<img>", {src:"icons/"+friend[1]+".png", class:friend[1]});
                        status.append(indicator);
                        //status.html(friend[1]);
                        row.append(pic);
                        row.append(name);
                        row.append(status);
                        list.append(row);
                    });
                    $("#displaylist").empty();
                    $("#displaylist").append(list);
                }
            });
            setTimeout(display_friends, 2000)
        }

        // function to fetch and display received friend requests from database.
        function display_received_requests(){
            $.ajax({
                url: 'get_received_friend_requests.php',
                data: "username=<?php echo $username ?>",
                datatype: "json",
                success: function(data) {
                    var list = $.parseJSON(data);
                    $('#received_notifications').empty();
                    $('#received_notifications').append("<h5>Received</h5>");
                    if(list != "none") {
                        $.each(list, function(index, obj) {
                            var sender = obj.user1;
                            var message = sender+" wants to be friends";
                            var request = $('<p>');
                            request.append(message);
                            var acceptBtn = $('<img>', {class:'imageButton', src:'icons/ok.png', alt:'accept', title:'accept', onclick:'process_received_friend_request("'+sender+'", "accept")'})
                            var rejectBtn = $('<img>', {class:'imageButton', src:'icons/clear.png', alt:'reject', title:'reject', onclick:'process_received_friend_request("'+sender+'"   , "reject")'});
                            var blockBtn = $('<img>', {class:'imageButton', src:'icons/block.png', alt:'block', title:'block', onclick:'process_received_friend_request("'+sender+'"   , "block")'});
                            request.append(acceptBtn);
                            request.append(rejectBtn);
                            request.append(blockBtn);
                            $('#received_notifications').append(request);
                        })
                    } else {
                        $('#received_notifications').append("Nothing to show");
                    }
                }
            });
            setTimeout(display_received_requests, 2000);
        }

        // function to fetch and display sent friend requests from database.
        function display_sent_requests(){
            $.ajax({
                url: 'get_sent_friend_requests.php',
                data: "username=<?php echo $username ?>",
                datatype: "json",
                success: function(data) {
                    var list = $.parseJSON(data);
                    $('#sent_notifications').empty();
                    $('#sent_notifications').append("<h5>Sent</h5>");
                    if(list != "none") {
                        $.each(list, function(index, obj) {
                            var to = obj.user2;
                            var message = '';
                            var relation = obj.relation;
                            if (relation == 'pending') {
                                message = "Friend request to "+to+" pending";
                            } else if (relation == 'rejected'){
                                message = to+" rejected your friend request";
                            }/* else if (relation == 'blocked'){
                                message = to+" blocked you";
                            }*/ else if (relation == 'accepted'){
                                message = to+" accepted your friend request";
                            }
                            var request = $('<p>');
                            request.append(message);
                            var deleteBtn = $('<img>', {class:"imageButton", src:'icons/clear.png', alt:'delete', title:'delete', onclick:'process_sent_friend_request("'+to+'", "'+relation+'")'});
                            request.append(deleteBtn);
                            $('#sent_notifications').append(request);
                        })
                    } else {
                        $('#sent_notifications').append("Nothing to show");
                    }
                },
                error: function(data) {
                    alert("Error: "+data);
                }
            });
            setTimeout(display_sent_requests, 2000);
        }

        
        // Processing friend requests:
        // ==========================
        // When u1 sends a friend request to u2, an entry(for u1-u2) is inserted in the relationship table in the databse
        // with relation=pending. u2 is notified of the friend request.
        //
        // Update the relationship status in the database:
        // ==============================================
        // If u2 accepts a request from u1, then insert an entry u2-u1 with relation=friend in the relationship table.
        // Update the entry for u1-u2 with relation=accepted. u1 gets notified that u2 accepted his/her request.
        // When u1 ackowledges it(process_sent_friend_request), then u1-u2 relation is updated to friend.
        //
        // If u2 rejects request from u1, then update u1-u2 with relation=rejected. u1 gets notified.
        // When u1 ackowledges it(process_sent_friend_request), then remove the u1-u2 entry from the table. u1 can send another request to u2.
        //
        // If u2 blocks u1, then update u1-u2 relation to blocked. Also insert an entry u2-u1 with relation=blocked. u1 and u2 cannot send
        // requests to each other anymore.

        // function to process a received friend request.
        // from: friend request from.
        // action: accept/reject/block.
        function process_received_friend_request(from, action) {
            var result = true;
            if(action == "block") {
                result = confirm("Are you sure you want to block "+from+"? Action cannot be undone.");
            }
            if (result == true) {
                $.ajax({
                    url: 'process_received_friend_request.php',
                    data: "user=<?php echo $username ?>&from="+from+"&action="+action,
                    success: function(data) {
                        alert(data);
                    },
                    error: function(data) {
                        alert("Error: "+data);
                    }
                });
            }
        }


        // function to process a sent friend request.
    // to: friend request to.
    // action: pending/accepted/rejected.
    // if action = pending or rejected -> delete the sent request from relationship table.
    // if action = accepted -> update relation to 'friend' in relationship table.
        function process_sent_friend_request(to, action) {
            $.ajax({
                url: 'process_sent_friend_request.php',
                data: "user=<?php echo $username ?>&to="+to+"&action="+action,
                success: function(data) {
                    alert(data);
                }
            });
        }


        // When a friend's name is clicked on, a chat windw pops up with previous chat history.
        function popup_chat_window(f){
            var chat_window = window.open("popup_chat_window.php?username=<?php echo $username?>&friend="+f, "chat window "+f, "location=no, width=300, height=500, location=no");
        }


        // When 'Add Friend' button is clicked, display a form where you can enter the username
        // that you want to send a request to.
        $("#add_friend").click(function () {
            $("#popup_add_friend").show(500)
        });
        // Send a request and add the entry in the database.
        $("#add").click(function(event) {
            event.stopPropagation();
            friend = $("#friendname").val().replace(/\s+/g, '');
            if(friend == "") {
                alert("Enter a valid username");
            } else {
                $("#popup_add_friend").hide(400);
                $.ajax({
                    type: "POST",
                    url: "add_friend.php",
                    data: "username=<?php echo $username?>&friendname="+friend+"&add="+true,
                    success: function(result) {
                        var response = JSON.parse(result);
                        alert(response.message);
                    },
                    error: function(result) {
                        alert("error: "+JSON.parse(result));
                    }
                });
            }
            return false;
        });


        // When you click on your profile pic, display a form to upload the new picture.
        function popup_upload_profile_pic() {
            $("#popup_edit_profile_pic").show(500);
        };
        // Choose new profile picture file to upload
        $("#uploadBtnTrigger").click(function() {
            $("#new_picture").trigger('click');
        });
        $("#new_picture").change(function() {
            $("#picture_name").val($(this).val());
        });
        // Upload the new picture and update in database.
        $("#save").click(function(event) {
            event.stopPropagation();
            event.preventDefault();
            var file = $("#new_picture")[0].files[0];
            var formData = new FormData();
            formData.append('username', '<?php echo $username?>');
            formData.append('picture', file);
            
            $.ajax({
                type: "POST",
                url: "upload_profile_pic.php",
                enctype: 'multipart/form-data',
                processData : false,
                contentType : false, 
                data: formData,
                success : function(data) {
                    dataArray = data.split('::');
                    if(dataArray[0] == 'Success') {
                        $('#profile_pic').remove();
                        $('<img>', {id:"profile_pic", src:dataArray[2].trim(), alt:"change", title:"change"
                          , onclick:"popup_upload_profile_pic()"}).insertBefore("#username");
                        $(".popup_form").hide(400);
                    }
                    alert(dataArray[1]);
                }
            });
        });


        // Hide popup forms when cancelled
        $(".cancel").click(function(event) {
            event.stopPropagation();
            event.preventDefault();
            $(".popup_form").hide(400);
        });

    </script>
</body>
</html>
