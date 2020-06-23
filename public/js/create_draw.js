function hideChoice(){
    $('#choice').hide();
}

function displayChoice(){
    $('#choice').show();

}

$('#draw_type').change(function(){
    if($(this).val() == 'unique'){
        hideChoice();
    }else{
        displayChoice();
    }
});