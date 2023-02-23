$(document).ready(function () {
    $('.btnSearch').on('click', function(e) {
        const sampleLessonId = $(this).data('sample-lession');
        const title = $('input[name="title-' + sampleLessonId + '"]').val();
        const contentLessons = $('.content-lesson-' + sampleLessonId);
        contentLessons.each(function(element) {
            if (title === '' || $(this).data('title').includes(title)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    })    
});
