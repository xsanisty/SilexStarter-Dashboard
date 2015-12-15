$(document).ready(function(){
    $('ul.sidebar .active > a').click(function(e){
        e.preventDefault();
    });
    $('ul.sidebar .active > a').after('<div class="pointer"><div class="arrow"></div><div class="arrow_border"></div></div>');


    $('#silexstarter-about').click(function(e){
        e.preventDefault();
        $('#silexstarter-about-modal').modal('show');
    });

    $('[data-toggle="tooltip"]').tooltip()
});