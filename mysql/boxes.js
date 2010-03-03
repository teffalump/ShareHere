/* General outline of retrieving and displaying our little popups. (not all filled in obviously) */

$(function () {
    //Get data from area hovered and display it, and then remove it
    $(".network").hover(
        function {
            var dataString="network_id="+this.id //some sort of identifier for the area
            $.ajax({
                type: "POST",
                url: "users.php", //or whatever the script is
                data: dataString,
                success: function(info) {
                    var users=info.split(":"); //I hope this stuff is fast...
                    for (person in users)
                    {
                        //then make the popups
                    }
                }   
            });
        }, 
        function {
            $(this).find().remove(); //fill in with however one removes the boxes
        });
});
