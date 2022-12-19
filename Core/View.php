<?php

namespace Core;

class View
{

    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            //echo "$file not found";
            throw new \Exception("$file not found");
        }
    }

    public static function renderTemplate(string $template, array $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    /**
     * 
     */
    public static function getTemplate(string $template, array $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
            $twig->addGlobal('date', \App\Controllers\Balance::getDateValue());
            $twig->addGlobal('max_date', date('Y-m-t'));
            $twig->addGlobal('current_date', date('Y-m-d'));
            $twig->addGlobal('current_date_dot', date('d.m.Y'));
            $twig->addGlobal('max_date_dot', date('t.m.Y'));
            $twig->addGlobal(
                'last_month_start',
                date('d.m.Y', (mktime(0, 0, 0, date("m") - 1, 01, date("Y"))))
            );
            $twig->addGlobal(
                'last_month_end',
                date('d.m.Y', (mktime(0, 0, 0, date("m") - 1, cal_days_in_month(CAL_GREGORIAN, date("m") - 1, date("Y")), date("Y"))))
            );
            $twig->addGlobal('custom_date_start', \App\Controllers\Balance::getCustomDateStart());
            $twig->addGlobal('custom_date_end', \App\Controllers\Balance::getCustomDateEnd());
            $twig->addGlobal('pieChartExpenses', \App\Controllers\Expense::getPieChartExpenseData());
        }

        return $twig->render($template, $args);
    }
}
