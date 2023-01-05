<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{

    public function index(){
        $users = User::all();
        if ( sizeof($users) === 0 ){
            return 'no existen usuarios';
        }
        return $users;
    }
}
