<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Expenses;

/**
 * Profile controller
 */
class Expense extends Authenticated
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
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $expense = new Expenses($_POST);

        if ($expense->save()) {

            $this->redirect('/expense/success');
        } else {

            View::renderTemplate('Expense/add.html', [
                'expense' => $expense
            ]);
        }
    }

    /**
     * Show the profile
     */
    public function addAction()
    {
        View::renderTemplate('Expense/add.html', [
            'user' => $this->user
        ]);
    }

    public function successAction()
    {
        View::renderTemplate('Expense/success.html');
    }
    
    public function expensesAction()
    {
        $id = $this->route_params['id'];
        $date = $this->route_params['date'];

        echo json_encode(Expenses::getExpenseSumAssignedToUser($id, $date), JSON_UNESCAPED_UNICODE);
    }

    public function limitAction()
    {
        $id = $this->route_params['id'];

        echo json_encode(Expenses::getExpenseLimitAssignedToUser($id), JSON_UNESCAPED_UNICODE);
    }

    public function editExpenseCategoryAction()
    {
        $expense = new Expenses($_POST);

        $expense->edit();

        View::renderTemplate('Settings/show.html', [
            'expense' => $expense
        ]);
    }

    public function addExpenseCategoryAction()
    {
        $expense = new Expenses($_POST);

        $expense->addCategory();

        View::renderTemplate('Settings/show.html', [
            'expense' => $expense
        ]);

    }

    public function deleteExpenseCategoryAction()
    {
        $expense = new Expenses($_POST);

        $expense->deleteCategory();

        View::renderTemplate('Settings/show.html', [
            'expense' => $expense
        ]);
    }

    public function addPaymentCategoryAction()
    {
        $expense = new Expenses($_POST);

        $expense->addPaymentCategory();

        View::renderTemplate('Settings/show.html', [
            'expense' => $expense
        ]);
    }

    public function editPaymentCategoryAction()
    {
        $expense = new Expenses($_POST);

        $expense->editPayment();

        View::renderTemplate('Settings/show.html', [
            'expense' => $expense
        ]);
    }

    public function deletePaymentCategoryAction()
    {
        $expense = new Expenses($_POST);

        $expense->deletePayment();

        View::renderTemplate('Settings/show.html', [
            'expense' => $expense
        ]);
    }

    public function editSingleRecordCommentAction()
    {
        $expense = new Expenses($_POST);

        $expense->editSingleRecordComment();

        $this->redirect('/balance/show');

    }

    public function deleteSingleRecordAction()
    {
        $expense = new Expenses($_POST);

        $expense->deleteSingleRecord();

        $this->redirect('/balance/show');

    }
}
