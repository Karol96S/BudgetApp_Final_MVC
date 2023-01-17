//wroc do pozycji
function keepPosition() {
    sessionStorage.scrollTop = $(window).scrollTop();
}

function setPosition() {
    $(document).ready(function () {
        if ((sessionStorage.scrollTop != "undefined") && (sessionStorage.scrollTop != 0)) {
            $(window).scrollTop(sessionStorage.scrollTop);
            sessionStorage.scrollTop = 0;
        }
    });
}

function getPos(el) {
    // yay readability
    for (var lx=0, ly=0;
         el != null;
         lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
    return {x: lx,y: ly};
}

function rememberDate() {
    let select = document.getElementById('date');
    let date = select.value;
    sessionStorage.date = date;
}

function getOldDate() {
    let select = document.getElementById('date');
    let date = select.value;
    sessionStorage.oldDate = date;
}

function setSelectMenuDate() {
    if(sessionStorage.date) {
        document.getElementById('date').value = sessionStorage.date;
    }
}

if(document.getElementById('date')) {
document.getElementById('date').addEventListener("click", function() {
    rememberDate();
});
}

//Handle modals and scroll pos depending on page
if(document.getElementById('accordionIncomes')) {
    window.onload = function() {
    $("body").children().first().before($(".modal"));
    setPosition();
    showErrorModals();
    showSuccessModals();
    //adjustAccordion();
    }
} else {
document.addEventListener("DOMContentLoaded", function(event) {
    setPosition();
    showErrorModals();
    showSuccessModals();
    adjustAccordion();
  });}

function showErrorModals() {
    //check edit-income
    if (document.getElementById('errorEditIncome')) {
        let incomeId;
        let index;
        index = document.getElementById('errorEditIncome').value;
        incomeId = "editIncome" + index;
        document.getElementById('panelsStayOpen-collapseOne').className = "accordion-collapse collapse show";
        document.getElementById(incomeId).click();
    }

    //check add-income
    if (document.getElementById('errorAddIncome')) {
        document.getElementById('panelsStayOpen-collapseOne').className = "accordion-collapse collapse show";
        document.getElementById('addIncome').click();
    }

    //check edit-expense
    if (document.getElementById('errorEditExpense')) {
        let expenseId;
        let index;
        index = document.getElementById('errorEditExpense').value;
        expenseId = "editExpense" + index;
        document.getElementById('panelsStayOpen-collapseTwo').className = "accordion-collapse collapse show";
        document.getElementById(expenseId).click();
    }

    //check add-expense
    if (document.getElementById('errorAddExpense')) {
        document.getElementById('panelsStayOpen-collapseTwo').className = "accordion-collapse collapse show";
        document.getElementById('addExpense').click();
    }

    //check edit-payment
    if (document.getElementById('errorEditPayment')) {
        let paymentId;
        let index;
        index = document.getElementById('errorEditPayment').value;
        paymentId = "editPayment" + index;
        document.getElementById('panelsStayOpen-collapseThree').className = "accordion-collapse collapse show";
        document.getElementById(paymentId).click();
    }

    //check add-payment
    if (document.getElementById('errorAddPayment')) {
        document.getElementById('panelsStayOpen-collapseThree').className = "accordion-collapse collapse show";
        document.getElementById('addPayment').click();
    }

    //check username
    if (document.getElementById('errorEditName')) {
        document.getElementById('panelsStayOpen-collapseFour').className = "accordion-collapse collapse show";
        document.getElementById('editName').click();
    }

    //check password
    if (document.getElementById('errorEditPassword')) {
        document.getElementById('panelsStayOpen-collapseFour').className = "accordion-collapse collapse show";
        document.getElementById('editPassword').click();
    }

    //check expense-comment
    if (document.getElementById('singleExpenseCommentError')) {
        let expenseCommentId;
        let index;
        index = document.getElementById('commentErrorId').value;
        expenseCommentId = "editSingleExpenseCommentModal" + index;
        document.getElementById(expenseCommentId).click();
    }

    //check income-comment
    if (document.getElementById('singleIncomeCommentError')) {
        let incomeCommentId;
        let index;
        index = document.getElementById('commentErrorId').value;
        incomeCommentId = "editSingleIncomeCommentModal" + index;
        document.getElementById(incomeCommentId).click();
    }
}

function showSuccessModals() {
    //check edit category
    if (document.getElementById('successEditMessage')) {
        document.getElementById('successEdit').click();
    }

    //check add category
    if (document.getElementById('successAddMessage')) {
        document.getElementById('successAdd').click();
    }

    //check delete category
    if (document.getElementById('successDeleteMessage')) {
        document.getElementById('successDelete').click();
    }
}

function rememberState() {
        if ((localStorage.getItem("panelsStayOpen-collapseOne") === null) || (localStorage.getItem("panelsStayOpen-collapseOne") == "false")) {
            localStorage.setItem('panelsStayOpen-collapseOne', "true");
        }

        else {
            localStorage.setItem('panelsStayOpen-collapseOne', "false");
        }

}

function adjustAccordion() {
    if (localStorage['panelsStayOpen-collapseOne'] == "true") {
    document.getElementById('panelsStayOpen-collapseOne').className = "accordion-collapse collapse show";
    }

    else if (localStorage['panelsStayOpen-collapseOne'] == "false") {
    document.getElementById('panelsStayOpen-collapseOne').className = "accordion-collapse collapse";
    }
}
