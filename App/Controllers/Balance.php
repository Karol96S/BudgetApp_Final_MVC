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
        /**No date chosen */
        if (!isset($_POST['date']) && (!isset($_SESSION['date']))) {

            $this->income = Incomes::getIncome($this->user->id);
            $this->incomeByCategory = Incomes::getIncomeByCategory($this->user->id);
            $this->expense = Expenses::getExpense($this->user->id);
            $this->expenseByCategory = Expenses::getExpenseByCategory($this->user->id);
            $_SESSION['expenseByCategory'] = $this->expenseByCategory;
            $_SESSION['incomeByCategory'] = $this->incomeByCategory;

            $this->singleExpenseComment = Expenses::getSingleCommentInfo();
            $this->singleIncomeComment = Incomes::getSingleCommentInfo();
            if (isset($_SESSION['deleteExpenseStatus'])) {
                $this->deleteExpenseStatus = $_SESSION['deleteExpenseStatus'];
            } else $this->deleteExpenseStatus = false;
            if (isset($_SESSION['editExpenseStatus'])) {
                $this->editExpenseStatus = $_SESSION['editExpenseStatus'];
            } else $this->editExpenseStatus = false;
            if (isset($_SESSION['deleteIncomeStatus'])) {
                $this->deleteIncomeStatus = $_SESSION['deleteIncomeStatus'];
            } else $this->deleteIncomeStatus = false;
            if (isset($_SESSION['editIncomeStatus'])) {
                $this->editIncomeStatus = $_SESSION['editIncomeStatus'];
            } else $this->editIncomeStatus = false;
        }

        /**Current month chosen */
        if (isset($_POST['date'])) {

            if ($_POST['date'] == 'currentMonth') {
                $_SESSION['date'] = 'currentMonth';

                $this->income = Incomes::getIncome($this->user->id);
                $this->incomeByCategory = Incomes::getIncomeByCategory($this->user->id);
                $this->expense = Expenses::getExpense($this->user->id);
                $this->expenseByCategory = Expenses::getExpenseByCategory($this->user->id);
                $_SESSION['expenseByCategory'] = $this->expenseByCategory;
                $_SESSION['incomeByCategory'] = $this->incomeByCategory;

                $this->singleExpenseComment = Expenses::getSingleCommentInfo();
                $this->singleIncomeComment = Incomes::getSingleCommentInfo();
                if (isset($_SESSION['deleteExpenseStatus'])) {
                    $this->deleteExpenseStatus = $_SESSION['deleteExpenseStatus'];
                } else $this->deleteExpenseStatus = false;
                if (isset($_SESSION['editExpenseStatus'])) {
                    $this->editExpenseStatus = $_SESSION['editExpenseStatus'];
                } else $this->editExpenseStatus = false;
                if (isset($_SESSION['deleteIncomeStatus'])) {
                    $this->deleteIncomeStatus = $_SESSION['deleteIncomeStatus'];
                } else $this->deleteIncomeStatus = false;
                if (isset($_SESSION['editIncomeStatus'])) {
                    $this->editIncomeStatus = $_SESSION['editIncomeStatus'];
                } else $this->editIncomeStatus = false;

                /**Last month chosen */
            } else if ($_POST['date'] == 'lastMonth') {
                $_SESSION['date'] = 'lastMonth';

                $this->income = Incomes::getIncome($this->user->id, 'lastMonth');
                $this->incomeByCategory = Incomes::getIncomeByCategory($this->user->id, 'lastMonth');
                $this->expense = Expenses::getExpense($this->user->id, 'lastMonth');
                $this->expenseByCategory = Expenses::getExpenseByCategory($this->user->id, 'lastMonth');
                $_SESSION['expenseByCategory'] = $this->expenseByCategory;
                $_SESSION['incomeByCategory'] = $this->incomeByCategory;

                $this->singleExpenseComment = Expenses::getSingleCommentInfo();
                $this->singleIncomeComment = Incomes::getSingleCommentInfo();
                if (isset($_SESSION['deleteExpenseStatus'])) {
                    $this->deleteExpenseStatus = $_SESSION['deleteExpenseStatus'];
                } else $this->deleteExpenseStatus = false;
                if (isset($_SESSION['editExpenseStatus'])) {
                    $this->editExpenseStatus = $_SESSION['editExpenseStatus'];
                } else $this->editExpenseStatus = false;
                if (isset($_SESSION['deleteIncomeStatus'])) {
                    $this->deleteIncomeStatus = $_SESSION['deleteIncomeStatus'];
                } else $this->deleteIncomeStatus = false;
                if (isset($_SESSION['editIncomeStatus'])) {
                    $this->editIncomeStatus = $_SESSION['editIncomeStatus'];
                } else $this->editIncomeStatus = false;

                /**Custom date entered */
            } else if ($_POST['date'] == 'custom') {
                $_SESSION['date'] = 'custom';
                $_SESSION['dateStart'] = $_POST['dateStart'];
                $_SESSION['dateEnd'] = $_POST['dateEnd'];

                $this->income = Incomes::getIncome($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
                $this->incomeByCategory = Incomes::getIncomeByCategory($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
                $this->expense = Expenses::getExpense($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
                $this->expenseByCategory = Expenses::getExpenseByCategory($this->user->id, 'custom', $_POST['dateStart'], $_POST['dateEnd']);
                $_SESSION['expenseByCategory'] = $this->expenseByCategory;
                $_SESSION['incomeByCategory'] = $this->incomeByCategory;

                $this->singleExpenseComment = Expenses::getSingleCommentInfo();
                $this->singleIncomeComment = Incomes::getSingleCommentInfo();
                if (isset($_SESSION['deleteExpenseStatus'])) {
                    $this->deleteExpenseStatus = $_SESSION['deleteExpenseStatus'];
                } else $this->deleteExpenseStatus = false;
                if (isset($_SESSION['editExpenseStatus'])) {
                    $this->editExpenseStatus = $_SESSION['editExpenseStatus'];
                } else $this->editExpenseStatus = false;
                if (isset($_SESSION['deleteIncomeStatus'])) {
                    $this->deleteIncomeStatus = $_SESSION['deleteIncomeStatus'];
                } else $this->deleteIncomeStatus = false;
                if (isset($_SESSION['editIncomeStatus'])) {
                    $this->editIncomeStatus = $_SESSION['editIncomeStatus'];
                } else $this->editIncomeStatus = false;
            }
        } else if (isset($_SESSION['date'])) {

            /**Remember chosen month - current*/
            if ($_SESSION['date'] == 'currentMonth') {

                $this->income = Incomes::getIncome($this->user->id);
                $this->incomeByCategory = Incomes::getIncomeByCategory($this->user->id);
                $this->expense = Expenses::getExpense($this->user->id);
                $this->expenseByCategory = Expenses::getExpenseByCategory($this->user->id);
                $_SESSION['expenseByCategory'] = $this->expenseByCategory;
                $_SESSION['incomeByCategory'] = $this->incomeByCategory;

                $this->singleExpenseComment = Expenses::getSingleCommentInfo();
                $this->singleIncomeComment = Incomes::getSingleCommentInfo();
                if (isset($_SESSION['deleteExpenseStatus'])) {
                    $this->deleteExpenseStatus = $_SESSION['deleteExpenseStatus'];
                } else $this->deleteExpenseStatus = false;
                if (isset($_SESSION['editExpenseStatus'])) {
                    $this->editExpenseStatus = $_SESSION['editExpenseStatus'];
                } else $this->editExpenseStatus = false;
                if (isset($_SESSION['deleteIncomeStatus'])) {
                    $this->deleteIncomeStatus = $_SESSION['deleteIncomeStatus'];
                } else $this->deleteIncomeStatus = false;
                if (isset($_SESSION['editIncomeStatus'])) {
                    $this->editIncomeStatus = $_SESSION['editIncomeStatus'];
                } else $this->editIncomeStatus = false;
                
                /**Remember chosen month - last month */
            } else if ($_SESSION['date'] == 'lastMonth') {

                $this->income = Incomes::getIncome($this->user->id, 'lastMonth');
                $this->incomeByCategory = Incomes::getIncomeByCategory($this->user->id, 'lastMonth');
                $this->expense = Expenses::getExpense($this->user->id, 'lastMonth');
                $this->expenseByCategory = Expenses::getExpenseByCategory($this->user->id, 'lastMonth');
                $_SESSION['expenseByCategory'] = $this->expenseByCategory;
                $_SESSION['incomeByCategory'] = $this->incomeByCategory;

                $this->singleExpenseComment = Expenses::getSingleCommentInfo();
                $this->singleIncomeComment = Incomes::getSingleCommentInfo();
                if (isset($_SESSION['deleteExpenseStatus'])) {
                    $this->deleteExpenseStatus = $_SESSION['deleteExpenseStatus'];
                } else $this->deleteExpenseStatus = false;
                if (isset($_SESSION['editExpenseStatus'])) {
                    $this->editExpenseStatus = $_SESSION['editExpenseStatus'];
                } else $this->editExpenseStatus = false;
                if (isset($_SESSION['deleteIncomeStatus'])) {
                    $this->deleteIncomeStatus = $_SESSION['deleteIncomeStatus'];
                } else $this->deleteIncomeStatus = false;
                if (isset($_SESSION['editIncomeStatus'])) {
                    $this->editIncomeStatus = $_SESSION['editIncomeStatus'];
                } else $this->editIncomeStatus = false;

                /**Remember chosen date - custom */
            } else if ($_SESSION['date'] == 'custom') {

                $this->income = Incomes::getIncome($this->user->id, 'custom', $_SESSION['dateStart'], $_SESSION['dateEnd']);
                $this->incomeByCategory = Incomes::getIncomeByCategory($this->user->id, 'custom', $_SESSION['dateStart'], $_SESSION['dateEnd']);
                $this->expense = Expenses::getExpense($this->user->id, 'custom', $_SESSION['dateStart'], $_SESSION['dateEnd']);
                $this->expenseByCategory = Expenses::getExpenseByCategory($this->user->id, 'custom', $_SESSION['dateStart'], $_SESSION['dateEnd']);
                $_SESSION['expenseByCategory'] = $this->expenseByCategory;
                $_SESSION['incomeByCategory'] = $this->incomeByCategory;

                $this->singleExpenseComment = Expenses::getSingleCommentInfo();
                $this->singleIncomeComment = Incomes::getSingleCommentInfo();
                if (isset($_SESSION['deleteExpenseStatus'])) {
                    $this->deleteExpenseStatus = $_SESSION['deleteExpenseStatus'];
                } else $this->deleteExpenseStatus = false;
                if (isset($_SESSION['editExpenseStatus'])) {
                    $this->editExpenseStatus = $_SESSION['editExpenseStatus'];
                } else $this->editExpenseStatus = false;
                if (isset($_SESSION['deleteIncomeStatus'])) {
                    $this->deleteIncomeStatus = $_SESSION['deleteIncomeStatus'];
                } else $this->deleteIncomeStatus = false;
                if (isset($_SESSION['editIncomeStatus'])) {
                    $this->editIncomeStatus = $_SESSION['editIncomeStatus'];
                } else $this->editIncomeStatus = false;
            }
        }

        View::renderTemplate('/Balance/show.html', [
            'income' => $this->income,
            'incomeByCategory' => $this->incomeByCategory,
            'expense' => $this->expense,
            'expenseByCategory' => $this->expenseByCategory,
            'singleExpenseComment' => $this->singleExpenseComment,
            'deleteExpenseStatus' => $this->deleteExpenseStatus,
            'editExpenseStatus' => $this->editExpenseStatus,
            'singleIncomeComment' => $this->singleIncomeComment,
            'deleteIncomeStatus' => $this->deleteIncomeStatus,
            'editIncomeStatus' => $this->editIncomeStatus
        ]);

        unset($_SESSION['deleteExpenseStatus']);
        unset($_SESSION['editExpenseStatus']);
        unset($_SESSION['deleteIncomeStatus']);
        unset($_SESSION['editIncomeStatus']);
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
        } else if (isset($_SESSION['date'])) {
            return $_SESSION['date'];
        }
    }

    public static function getCustomDateStart()
    {
        if (isset($_POST['date']) && isset($_POST['dateStart'])) {
            $userInputDateStart = $_POST['dateStart'];
            return date('d.m.Y', strtotime($userInputDateStart));
        } else if (isset($_SESSION['date']) && isset($_SESSION['dateStart'])) {
            $userInputDateStart = $_SESSION['dateStart'];
            return date('d.m.Y', strtotime($userInputDateStart));
        }
    }

    public static function getCustomDateEnd()
    {
        if (isset($_POST['date']) && isset($_POST['dateEnd'])) {
            $userInputDateEnd = $_POST['dateEnd'];
            return date('d.m.Y', strtotime($userInputDateEnd));
        } else if (isset($_SESSION['date']) && isset($_SESSION['dateEnd'])) {
            $userInputDateStart = $_SESSION['dateEnd'];
            return date('d.m.Y', strtotime($userInputDateStart));
        }
    }
}
