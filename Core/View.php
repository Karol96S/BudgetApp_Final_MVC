<?php

namespace Core;

class View{

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
 
        if ($twig === null)
        {
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
            $twig->addGlobal('max_date', date('Y-m-t'));
            $twig->addGlobal('current_date', date('Y-m-d'));
        }
 
        return $twig->render($template, $args);
    }
}