
$(document).ready(function() {
    $('.chips-initial').material_chip({
        data: [
            <?php
                foreach ($rows as $course) {
                    echo "{tag: '$course'},";
                }
            ?>
        ],
    });

    /* Adding course */
    $('.chips').on('chip.add', function(e, chip) {
        var course = chip.tag;
        $.post('/add_course.php', {'course' : course})
            .done(function(json) {
                if (json['success'] == true) {
                    Materialize.toast('<span>Successfully added course!</span>', 5000);
                } else {
                    Materialize.toast('<span>Failed to add course.<br>Please try again.</span>', 5000);
                }
            });
    });

    /* Deleting course */
    $('.chips').on('chip.delete', function(e, chip) {
        var course = chip.tag;
        $.post('/delete_course.php', {'course' : course})
            .done(function(json) {
                if (json['success'] == true) {
                    Materialize.toast('<span>Successfully deleted course!</span>', 5000);
                } else {
                    Materialize.toast('<span>Failed to deleted course.<br>Please try again.</span>', 5000);
                }
            });
    });
});
