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
        if( !isset($_POST['date']) || ($_POST['date'] == 'currentMonth') ) {
        $this->income = Income::getIncome($this->user->id);
        $this->expense = Expense::getExpense($this->user->id);
        }

        else if ($_POST['date'] == 'lastMonth') {
            $this->income = Income::getIncome($this->user->id, 'lastMonth');
            $this->expense = Expense::getExpense($this->user->id, 'lastMonth');
        }

        else if ($_POST['date'] == 'custom') {
            $this->income = Income::getIncome($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
            $this->expense = Expense::getExpense($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
        }

        View::renderTemplate('/Balance/show.html', [
            'income' => $this->income,
            'expense' => $this->expense
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
        return $_POST['dateStart'];
        }
    }

    public static function getCustomDateEnd()
    {
        if (isset($_POST['date'])) {
        return $_POST['dateEnd'];
        }
    }
}
