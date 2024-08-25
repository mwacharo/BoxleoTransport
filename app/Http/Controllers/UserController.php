<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
  public function index(){

    $user_id = auth()->user()->id;

    return view('users.index',['user_id' => $user_id]);
  }

  public function userAcount(){
    return view('users.user-account');
  }

  public function acountSettings(){
    return view('users.account-settings');
  }

  public function userRoles(){
    return view('users.user-roles');
  }
  public function roles(){
    $user_id = auth()->user()->id;

    return view('users.roles',['user_id' => $user_id]);
  }
  public function permissions(){
    $user_id = auth()->user()->id;

    return view('users.permissions',['user_id' => $user_id]);
  }
}