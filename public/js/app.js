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

function rememberDate() {
    let select = document.getElementById('date');
    let date = select.value;
    sessionStorage.date = date;
}

function getOldDate() {
    let select = document.getElementById('date');
    let date = select.value;
    sessionStorage.date = date;
}

function setSelectMenuDate() {
    if(sessionStorage.date) {
        document.getElementById('date').value = sessionStorage.date;
    }
}

document.getElementById('date').addEventListener("click", function() {
    rememberDate();
});

document.addEventListener("DOMContentLoaded", function(event) {
    //$("body").children().first().before($(".modal"));
    setPosition();
    showErrorModals();
    showSuccessModals();
    //adjustAccordion();
  });

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
    console.log('wtf1');
    if (document.getElementById('singleExpenseCommentError')) {
        console.log('wtf2');
        let expenseCommentId;
        let index;
        index = document.getElementById('singleExpenseId').value;
        console.log('tu');
        expenseCommentId = "editSingleExpenseCommentModal" + index;
        document.getElementById(expenseCommentId).click();
        console.log('eh', expenseCommentId)
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

/*
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
}*/
