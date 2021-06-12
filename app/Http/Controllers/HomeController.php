<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        //Redirecting the users on the basis of their roles
        $userRole = auth()->user()->getRoleNames()->first();
        switch ($userRole) {
            case 'admin':
                return redirect(route('admin.dashboard'));
                break;

            case 'shipper':
                return view('shipper.index',['admins' => User::all()]);//gets only order management permissions in admin dashboard
                break;

            default:
                return redirect(route('user.index'));
                break;
        }
    }
}
