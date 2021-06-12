<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Traits\RouteRoleTrait;

class UserController extends Controller
{
    use RouteRoleTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }
    //user profile 
    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->hasRole('shipper')) {
            return view('shipper.index',['admins' => User::all()]);
        }
            return view('user.index');
    }

    //post route
    public function address(Request $request)
    {
        $request->validate([
            'address' => 'required|min:3',
            'phone' => 'required|min:6'
        ]);

        $userInfo = UserInfo::updateOrCreate([
            'user_id' => auth()->user()->id,
            'address' => $request->address,
            'phone' => $request->phone
        ]);
        Alert::toast('Shipping info updated!', 'success');
        return view('user.index');
    }

    public function destroy($id){
        User::destroy($id);
        return view('shipper.index',['admins' => User::all()]);
    }

    public function newAdmin(Request $request){
        $input = ['name' => $request->name,
                'email' => $request->email,
                'password' => $request->pass
            ];

        CreateNewUser::createAdmin($input);
        return view('shipper.index',['admins' => User::all()]);
    }
}
