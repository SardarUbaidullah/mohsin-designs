<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontController extends Controller
{
public function team(){
   return Inertia::render('projectmanager/TeamMember');
}
public function test(){
    return view('test');
}
}
