<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;
use \App\Models\Incomes;
use \App\Models\Expenses;

class Settings extends Authenticated
{
    
    protected function before()
    {
        parent::before();
        
        $this->user = Auth::getUser();
    }

    /**
     * Show the profile
     */
    public function showAction()
    {
        View::renderTemplate('Settings/show.html', [
            'user' => $this->user
        ]);
    }

    public function deleteRecordsAction()
    {
        if((Incomes::deleteIncomeRecordsAssignedToUser()) && (Expenses::deleteExpenseRecordsAssignedToUser())) {
            $delete['status'] = true;
        }
        
        View::renderTemplate('Settings/show.html', [
            'delete' => $delete['status']
        ]);
    }

}