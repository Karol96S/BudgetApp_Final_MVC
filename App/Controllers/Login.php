<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Login extends \Core\Controller
{
    public function newAction()
    {
        if (Auth::getUser()) {

            $this->redirect(Auth::getReturnToPage());
        } else {

        View::renderTemplate('Login/new.html');
        
        }
    }

    public function createAction()
    {
        $user = User::authehticate($_POST['email'], $_POST['password']);

        $remember_me = isset($_POST['remember_me']);

        if ($user) {

            Auth::login($user, $remember_me);

            //$this->redirect("/profile/show");
            $this->redirect(Auth::getReturnToPage());

        } else {

            Flash::addMessage('Niepoprawne logowanie, spróbuj ponownie!', FLASH::WARNING);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ]);
        }
    }

    /**
     * Log out user
     */
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    public function showLogoutMessageAction()
    {
        Flash::addMessage('Wylogowano poprawnie!');

        $this->redirect('/'); 
    }
}
