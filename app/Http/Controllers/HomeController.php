<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{


    public function index()
    {
        $user = User::all();
        $a = 'Data User';
        return view('layout.main', compact('user', 'a'));
    }
    public function dashboard()
    {
        return view('tampilan.main');
    }
}
