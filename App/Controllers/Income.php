<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Incomes;

/**
 * Profile controller
 */
class Income extends Authenticated
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
        $income = new Incomes($_POST);

        if ($income->save()) {

            $this->redirect('/income/success');

        } else {

            View::renderTemplate('Income/add.html', [
                'income' => $income
            ]);

        }
    }

    /**
     * Show the profile
     */
    public function addAction()
    {
        View::renderTemplate('Income/add.html', [
            'user' => $this->user
        ]);
    }

    public function successAction()
    {
        View::renderTemplate('Income/success.html');
    }

    public function editIncomeCategoryAction()
    {
        $income = new Incomes($_POST);

        $income->edit();

        View::renderTemplate('Settings/show.html', [
            'income' => $income
        ]);
    }

    public function addIncomeCategoryAction()
    {
        $income = new Incomes($_POST);

        $income->addCategory();

        View::renderTemplate('Settings/show.html', [
            'income' => $income
        ]);

    }

    public function deleteIncomeCategoryAction()
    {
        $income = new Incomes($_POST);

        $income->deleteCategory();

        View::renderTemplate('Settings/show.html', [
            'income' => $income
        ]);
    }

    public function editSingleRecordCommentAction()
    {
        $income = new Incomes($_POST);

        $income->editSingleRecordComment();

        $this->redirect('/balance/show');

    }

    public function deleteSingleRecordAction()
    {
        $income = new Incomes($_POST);

        $income->deleteSingleRecord();

        $this->redirect('/balance/show');

    }

}