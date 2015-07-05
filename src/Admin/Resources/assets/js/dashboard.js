$(document).ready(function(){
    $('ul.sidebar .active a').after('<div class="pointer"><div class="arrow"></div><div class="arrow_border"></div></div>');

    $('#silexstarter-about').click(function(e){
        e.preventDefault();
        $('#silexstarter-about-modal').modal('show');
    });
});