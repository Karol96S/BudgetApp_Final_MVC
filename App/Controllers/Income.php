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

    public static function getIncome($user_ID, $choice = null, $dateStart = null, $dateEnd= null)
    {
        $income = new Incomes;

        if ($choice == 'currentMonth' || ($choice == null)) {
            return $income->currentMonthIncomes($user_ID);
        }

        else if ($choice == 'lastMonth') {
            return $income->lastMonthIncomes($user_ID);
        }

        else if ($choice == 'custom') {
            return $income->lastMonthIncomes($user_ID, $dateStart, $dateEnd);
        }

    }

}