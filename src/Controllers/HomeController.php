<?php

namespace Controllers;

use Core\Controller;
use Core\View;

/**
 * Home Controller
 */
class HomeController extends Controller
{
    /**
     * Show the home page
     */
    public function indexAction(): void
    {
        $data = [
            'title' => 'Welcome to Camagru',
            'message' => 'This is the home page!'
        ];

        View::render('home/index', $data);
    }
}