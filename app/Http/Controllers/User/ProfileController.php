<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\DepositStoreMail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfileController extends Controller
{
    public function profile()
    {
        
        $trans = Transaction::where('user_id', Auth::user()->id)->where('status', 1)->get();
        $totalDeposit = Transaction::where('user_id', Auth::user()->id)->where('status', 1)->sum('amount');
        return view('user.profile', compact('trans','totalDeposit'));
    } 

    public function profileUpdate(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::user()->id,
            'phone' => 'required|string|max:15',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::findOrFail(Auth::user()->id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('coverimage')) {
            if ($user->coverimage && file_exists(public_path($user->coverimage))) {
                unlink(public_path($user->coverimage));
            }

            $image = $request->file('coverimage');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/users'), $imageName);
            $user->coverimage = '/images/users/' . $imageName;
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

        return back()->with('success', 'Your information has been saved successfully');
    }

    public function addMoney()
    {
        $trans = Transaction::where('user_id', Auth::user()->id)->where('status', 0)->get();
        return view('user.addmoney', compact('trans'));
    } 

    public function addMoneyStore(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        do {
            $tranid = random_int(100000, 999999);
        } while (Transaction::where('tranid', $tranid)->exists()); 

        $trandata = new Transaction();
        $trandata->user_id = Auth::user()->id;
        $trandata->tranid = $tranid;
        $trandata->date = $request->date;
        $trandata->due = $request->due;
        $trandata->amount = $request->amount;
        $trandata->last_digit = $request->last_digit;
        $trandata->note = $request->note;
        if ($request->hasFile('document')) {
            if ($trandata->document && file_exists(public_path($trandata->document))) {
                unlink(public_path($trandata->document));
            }

            $image = $request->file('document');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/document'), $imageName);
            $trandata->document = '/images/document/' . $imageName;
        }
        if ($trandata->save()) {

            $user = User::with('transactions')->where('id', Auth::user()->id)
                            ->withSum('transactions as total_fine', 'fine')
                            ->withSum('transactions as total_amount', 'amount')
                            ->first();
            $data = Transaction::where('id',$trandata->id)->get();
            $pdf = PDF::loadView('invoices.depositor', compact('user','data'));
            $output = $pdf->output();
            file_put_contents(public_path().'/invoices/'.'deposit#'.$trandata->id.'.pdf', $output);
            $array['file'] = public_path().'/invoices/deposit#'.$trandata->id.'.pdf';
            $array['user'] = $user;
            $array['tamnt'] = $request->amount;
            Mail::to(Auth::user()->email)
            ->send(new DepositStoreMail($array));
            unlink(public_path().'/invoices/'.'deposit#'.$trandata->id.'.pdf');
            return back()->with('success', 'Your transaction has been saved successfully');
        } else {
            return back()->with('error', 'Your internet connection error. Please try again later.');
        }
        

    }

    public function tranDelete($id)
    {

        $data = Transaction::findOrFail($id);
        if ($data->document && file_exists(public_path($data->document))) {
            unlink(public_path($data->document));
        }
        $data->delete();

        $trans = Transaction::where('user_id', Auth::user()->id)->where('status', 0)->get();
        return back()->with('deletesuccess', 'Your transaction has been delete successfully');
    } 
}
