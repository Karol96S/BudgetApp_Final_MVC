//get id value from select menu
const selectedCategory = document.getElementById('selectMenuCategoryOfExpense');
const selectedDate = document.getElementById('date');
let id = selectedCategory.value;
let date = selectedDate.value; //yyyy-mm-dd
let moneySpent = 0;
let limit = 0;
let expensesSum = 0;
let spendings = 0;
let inputAmount = null;
let sum = null;

const getSumOfExpensesForSelectedMonth = async () => {
    try{
        const result = await fetch(`/api/expenses/${id}/${date}`);
        const data = await result.json();
        return await data['category_expenses'];
    } catch (e) {
        console.log('error', e);
    }
}

const getLimitForCategory = async () => {
    try{
    const result = await fetch(`/api/limit/${id}`);
    const data = await result.json();

    return await data['category_limit'];
        } catch (e) { 
            console.log('error', e);
        }
    }

const calculateSpendings = async () => {
    expensesSum = await getSumOfExpensesForSelectedMonth();
    expensesSum = Number(expensesSum);
    inputAmount = Number(inputAmount);

    if(inputAmount) moneySpent = expensesSum + inputAmount;
    else if (expensesSum === null) moneySpent = 0;
    else moneySpent = expensesSum;

    return moneySpent;
}

const calculateLimits = async () => {
    limit = await getLimitForCategory()
    spendings = await calculateSpendings();
    limit = Number(limit);
    spendings = Number(spendings);
    const checkIfLimitIsPassed = limit - spendings;

    return checkIfLimitIsPassed;
}

//Change in amount
const amountChange = async (newAmount) => {
    inputAmount = newAmount;
    sum = await calculateLimits();
    renderDOM();
}

//Date change
const checkLimit = async () => {
    date = document.getElementById('date').value;
    expensesSum = await getSumOfExpensesForSelectedMonth();
    sum = await calculateLimits();
    renderDOM();
};

//Category change
const checkCategory = async () => {
    id = document.getElementById('selectMenuCategoryOfExpense').value;
    sum = await calculateLimits();
    renderDOM();
}

const renderDOM = () => {
    if ((limit !== undefined) && (limit > 0) && (sum >= 0)) {
        document.getElementById("basicSummary").className = "alert alert-success row p-0 mt-2 mx-0 mx-md-4";
        document.getElementById('expenseDynamicSummary').innerHTML = "<b>Zostało:</b>";
        document.getElementById('limit').innerHTML = limit.toFixed(2) + " zł";
        document.getElementById('moneySpent').innerHTML = moneySpent.toFixed(2) + " zł";
        document.getElementById('moneyRemaining').innerHTML = sum.toFixed(2) + " zł";
        document.getElementById('basicSummary').style.display = 'flex';
    }

    else if ((limit !== undefined) && (limit > 0) && (sum < 0)) {
        document.getElementById("basicSummary").className = "alert alert-danger row p-0 mt-2 mx-0 mx-md-4";
        document.getElementById('expenseDynamicSummary').innerHTML = "<b>Poza limitem:</b>";
        document.getElementById('limit').innerHTML = limit.toFixed(2) + " zł";
        document.getElementById('moneySpent').innerHTML = moneySpent.toFixed(2) + " zł";
        document.getElementById('moneyRemaining').innerHTML = sum.toFixed(2) + " zł";
        document.getElementById('basicSummary').style.display = 'flex';
    }

    else {
        document.getElementById('basicSummary').style.display = 'none';
    }
} 