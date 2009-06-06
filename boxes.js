/* General outline of retrieving and displaying our little popups. (not all filled in obviously) */

$(function () {
    //Get data from area hovered and display it, and then remove it
    $(".network").hover(
        function {
            var datastring="network="+this.id //some sort of identifier for the area
            $.ajax({
                type: "POST",
                url: "info.php", //or whatever the script is
                data: dataString,
                success: function(info) {
                    for (person in info)
                    {
                        //then make the popups
                    }
                }   
            });
        }, 
        function {
            $(this).find().remove() //fill in with however one removes the boxes
        }
});
