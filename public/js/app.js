

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
