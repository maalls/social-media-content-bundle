$(document).ready(function() {

    $(".ajax-toggle-btn").click(function() {

        var btn = $(this);
        $.get(btn.attr("href"), function(data) {

            console.log(data);
            if(data.toggle == 0) {

                btn.removeClass('btn-primary');
                btn.addClass('btn-white');

            }
            else {

                btn.removeClass('btn-white');
                btn.addClass('btn-primary');

            }

        });

        return false;

    });


});