<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function follow(string $id)
    {
      $user = \Auth::user();
      $follow_user = User::where('id', $id)->first();

      // $user->follows()->attach($user->id);
      $user->followers()->attach($follow_user->id);
    }

    public function unfollow(string $id)
    {
      $user = \Auth::user();
      $follow_user = User::where('id', $id)->first();

      // $user->follows()->detach($user->id);
      $user->followers()->detach($follow_user->id);
    }
}
