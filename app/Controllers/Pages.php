<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Pages extends BaseController
{
    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
    }

    public function home()
    {
        $data = ['title' => 'Home'];

        return view('pages/home', $data);
    }

    public function profile()
    {
        $data = [
            'title' => 'Profile',
            'user' => $this->usersModel->getUser('id', session()->get('id'))
        ];

        return view('pages/profile', $data);
    }
}
