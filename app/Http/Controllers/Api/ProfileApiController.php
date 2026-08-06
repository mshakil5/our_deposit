<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileApiController extends Controller
{
    // ==================== GET PROFILE ====================
    public function profile()
    {
        $user = Auth::user();
        $trans = Transaction::where('user_id', $user->id)->where('status', 1)->get();
        $totalDeposit = Transaction::where('user_id', $user->id)->where('status', 1)->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'transactions' => $trans,
                'total_deposit' => $totalDeposit
            ]
        ]);
    }

    // ==================== UPDATE PROFILE ====================
    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::user()->id,
            'phone' => 'required|string|max:15',
            'password' => 'nullable|string|min:6|confirmed',
            'coverimage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'profileimage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail(Auth::user()->id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Cover Image Upload
        if ($request->hasFile('coverimage')) {
            if ($user->coverimage && file_exists(public_path($user->coverimage))) {
                unlink(public_path($user->coverimage));
            }

            $image = $request->file('coverimage');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/users'), $imageName);
            $user->coverimage = '/images/users/' . $imageName;
        }

        // Profile Image Upload
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

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    // ==================== GET INSTALLMENTS (Pending) ====================
    public function getInstallments()
    {
        $trans = Transaction::where('user_id', Auth::user()->id)
                            ->where('status', 0)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $trans
        ]);
    }

    // ==================== ADD INSTALLMENT ====================
    public function addInstallment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer',
            'date' => 'nullable|date',
            'due' => 'nullable|string',
            'last_digit' => 'nullable|string|max:10',
            'note' => 'nullable|string',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate unique transaction ID
        do {
            $tranid = random_int(100000, 999999);
        } while (Transaction::where('tranid', $tranid)->exists());

        $trandata = new Transaction();
        $trandata->user_id = Auth::user()->id;
        $trandata->tranid = $tranid;
        $trandata->date = $request->date ?? now()->format('Y-m-d');
        $trandata->due = $request->due;
        $trandata->amount = $request->amount;
        $trandata->last_digit = $request->last_digit;
        $trandata->note = $request->note;
        $trandata->tran_type = 'Deposit';

        // Document Upload
        if ($request->hasFile('document')) {
            $image = $request->file('document');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/document'), $imageName);
            $trandata->document = '/images/document/' . $imageName;
        }

        $trandata->save();

        // Note: PDF generation and email sending removed for API
        // You can add it back if needed with queue jobs

        return response()->json([
            'success' => true,
            'message' => 'Installment added successfully',
            'data' => $trandata
        ], 201);
    }

    // ==================== UPDATE INSTALLMENT ====================
    public function updateInstallment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:transactions,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'last_digit' => 'nullable|string|max:10',
            'note' => 'nullable|string',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5024'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify transaction belongs to user
        $tran = Transaction::where('id', $request->id)
                          ->where('user_id', Auth::user()->id)
                          ->firstOrFail();

        $tran->date = $request->date;
        $tran->amount = $request->amount;
        $tran->last_digit = $request->last_digit;
        $tran->note = $request->note;

        if ($request->hasFile('document')) {
            if ($tran->document && file_exists(public_path($tran->document))) {
                @unlink(public_path($tran->document));
            }

            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/document'), $filename);
            $tran->document = '/images/document/' . $filename;
        }

        $tran->save();

        return response()->json([
            'success' => true,
            'message' => 'Installment updated successfully',
            'data' => $tran
        ]);
    }

    // ==================== DELETE INSTALLMENT ====================
    public function deleteInstallment($id)
    {
        $tran = Transaction::where('id', $id)
                          ->where('user_id', Auth::user()->id)
                          ->firstOrFail();

        if ($tran->document && file_exists(public_path($tran->document))) {
            unlink(public_path($tran->document));
        }

        $tran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Installment deleted successfully'
        ]);
    }
}