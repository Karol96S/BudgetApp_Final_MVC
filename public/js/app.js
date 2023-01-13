

//In eddit box -> if checkbox is checked then input field enables
/*
document.getElementById('editExpenseLimitCheckbox').onchange = function () {
    document.getElementById('editExpenseLimit').disabled = !this.checked;
    };
    
document.getElementById('addExpenseLimitCheckbox').onchange = function () {
document.getElementById('addExpenseLimit').disabled = !this.checked;
    };
*/

//wroc do pozycji
function keepPosition() {
    sessionStorage.scrollTop = $(window).scrollTop();
    scrollPos = $(window).scrollTop();
}

function setPosition() {
    $(document).ready(function () {
        if (sessionStorage.scrollTop != "undefined") {
            $(window).scrollTop(sessionStorage.scrollTop);
        }
    });
}

window.onload = function() {
    setPosition();
};

jQuery(document).ready(function(){
    var act = 0;
    $( "#accordion" ).accordion({
        create: function(event, ui) {
            //get index in cookie on accordion create event
            if($.cookie('saved_index') != null){
               act =  parseInt($.cookie('saved_index'));
            }
        },
        change: function(event, ui) {
            //set cookie for current index on change event
            $.cookie('saved_index', null);
            $.cookie('saved_index', ui.options.active);
        },
        active:parseInt($.cookie('saved_index'))
    });
});