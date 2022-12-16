<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Incomes;
use \App\Models\Expenses;

/**
 * Profile controller
 */
class Balance extends Authenticated
{
    /**
     * Before filter - called before each action method
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Render balance page
     */
    public function showAction()
    {
        if (!isset($_POST['date']) || ($_POST['date'] == 'currentMonth')) {

            $this->income = Income::getIncome($this->user->id);
            $this->incomeByCategory = Income::getIncomeByCategory($this->user->id);

            $this->expense = Expense::getExpense($this->user->id);
            $this->expenseByCategory = Expense::getExpenseByCategory($this->user->id);
            //$this->pieChart = static::PieChartData($this->expenseByCategory);

        } else if ($_POST['date'] == 'lastMonth') {

            $this->income = Income::getIncome($this->user->id, 'lastMonth');
            $this->incomeByCategory = Income::getIncomeByCategory($this->user->id, 'lastMonth');

            $this->expense = Expense::getExpense($this->user->id, 'lastMonth');
            $this->expenseByCategory = Expense::getExpenseByCategory($this->user->id, 'lastMonth');
            //$this->pieChart = static::PieChartData($this->expenseByCategory);

        } else if ($_POST['date'] == 'custom') {

            $this->income = Income::getIncome($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
            $this->incomeByCategory = Income::getIncomeByCategory($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
            
            $this->expense = Expense::getExpense($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
            $this->expenseByCategory = Expense::getExpenseByCategory($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
            //$this->pieChart = static::PieChartData($this->expenseByCategory);
        }

        View::renderTemplate('/Balance/show.html', [
            'income' => $this->income,
            'incomeByCategory' => $this->incomeByCategory,
            'expense' => $this->expense,
            'expenseByCategory' => $this->expenseByCategory
        ]);
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $income = new Incomes($_POST);

        if ($income->save()) {

            $this->redirect('/income/success');
        } else {

            View::renderTemplate('Income/add.html', [
                'income' => $income
            ]);
        }
    }

    public static function getDateValue()
    {
        if (isset($_POST['date'])) {
            return $_POST['date'];
        }
    }

    public static function getCustomDateStart()
    {
        if (isset($_POST['date'])) {
            $userInputDateStart = $_POST['dateStart'];
            return date('d.m.Y', strtotime($userInputDateStart));
        }
    }

    public static function getCustomDateEnd()
    {
        if (isset($_POST['date'])) {
            $userInputDateEnd = $_POST['dateEnd'];
            return date('d.m.Y', strtotime($userInputDateEnd));
        }
    }

    /*public static function PieChartData($expenseByCategory)
    {
        $pieChartData = [];
        $pieChartData['dataPoints'] = array();
        $pieChartData['expenseSum'] = 0;
        $pieChartData['expensePercentage'] = 0;

        foreach ($expenseByCategory as $data) {
            $pieChartData['expenseSum'] = $data['amount'] + $pieChartData['expenseSum'];
        }

        foreach ($expenseByCategory as $data) {
            $pieChartData['expensePercentage'] = $data['amount'] / $pieChartData['expenseSum'] * 100;
            $newArray = array("label" => $data['name'], "y" => $pieChartData['expensePercentage']);
            array_push($pieChartData['dataPoints'], $newArray);
        }

        return $pieChartData;
    }*/

}
