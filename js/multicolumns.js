$(document).ready(function(){
    var max_fields      = 20; //maximum input boxes allowed
    var wrapper         = $(".container-feeds"); 
    var add_button      = $(".add_field");
   
    var x = 1; 
   
    $(wrapper).on("click",".remove_field", function(e){ 
        e.preventDefault(); 
        $(this).parents('div.bloc').remove(); x--;
    })

    $(wrapper).on("click",".add_field", function(e){ 
        e.preventDefault(); 
        e.preventDefault();
        if(x < max_fields){ 
            x++;
            $n = $('.bloc').first().html();
            
            
            $(wrapper).append('<div class="bloc mcw-bloc">'+$n+'</div>').find('.bloc').last().find('.buttons').append('<a href="#" class="remove_field">Remove</a>');
            

        }
    })
});