<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $data = User::where('is_type', '0')->orderby('id','DESC')->get();
        return view('admin.users.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed',
            'profileimage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->is_type = '0';
        $user->password = Hash::make($request->password);

        if ($request->hasFile('profileimage')) {
            $image = $request->file('profileimage');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/users'), $imageName);
            $user->profileimage = '/images/users/' . $imageName;
        }

        $user->save();

        return response()->json(['status' => 200, 'message' => 'User  created successfully.']);
    }

    public function edit($id)
    {
        $data = User::where('id', $id)->first();
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->codeid,
            'phone' => 'required|string|max:15',
            'password' => 'nullable|string|min:6',
            'profileimage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = $request->all();
        $user = User::find($request->codeid);

        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if (isset($request->password)) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profileimage')) {
            if ($user->profileimage && file_exists(public_path($user->profileimage))) {
                unlink(public_path($user->profileimage));
            }

            $image = $request->file('profileimage');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/users'), $imageName);
            $user->profileimage = '/images/users/' . $imageName;
        }

        $user->save();

        return response()->json(['status' => 200, 'message' => 'User updated successfully.', 'data' => $data]);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        if ($user->profileimage && file_exists(public_path($user->profileimage))) {
            unlink(public_path($user->profileimage));
        }

        $user->delete();

        return response()->json(['status' => 200, 'message' => 'User deleted successfully.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
    }
    
}