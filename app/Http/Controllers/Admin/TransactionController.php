<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index($id = null)
    {
        $data = Transaction::orderby('id', 'DESC')
        ->when($id, function($query) use ($id) {
            return $query->where('user_id', $id);
        })
        ->where('status', 1)->get();
        return view('admin.transaction.index', compact('data'));
    }

    public function pending()
    {
        $data = Transaction::orderby('id', 'DESC')->where('status', 0)->get();
        return view('admin.transaction.pending', compact('data'));
    }

    public function updateStatus(Request $request)
    {
        $data = Transaction::findOrFail($request->tranId);
        $data->status = $request->status;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
    }

    public function edit($id)
    {
        $data = Transaction::whereId($id)->first();
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|max:255',
            'amount' => 'required',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Transaction::findOrFail($request->codeid);
        $data->date = $request->date;
        $data->last_digit = $request->last_digit;
        $data->amount = $request->amount;
        $data->fine = $request->fine;
        $data->note = $request->note;

        if ($request->hasFile('document')) {
            if ($data->document && file_exists(public_path($data->document))) {
                unlink(public_path($data->document));
            }
            $image = $request->file('document');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/document'), $imageName);
            $data->document = '/images/document/' . $imageName;
        }

        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }

    public function missingDeposit()
    {

        $users = User::where('is_type', '0')->where('status', 1)->orderby('id','DESC')->get();

        // Fetch deposits grouped by month and user
        $deposits = Transaction::select(
            DB::raw('DATE_FORMAT(STR_TO_DATE(date, "%Y-%m-%d"), "%Y-%m") as month'),
            'user_id',
            DB::raw('SUM(amount) as total_amount')
        )
            ->groupBy('month', 'user_id')
            ->get()
            ->groupBy('month');

        // Get unique months from deposits
        $months = $deposits->keys()->sort()->values();

        // Prepare the report data
        $report = [];
        $columnSums = []; // For column-wise sums (total per month)
        $rowSums = []; // For row-wise sums (total per user)

        // Initialize row sums
        foreach ($users as $user) {
            $rowSums[$user->id] = 0;
        }

        // Process deposits and calculate sums
        foreach ($months as $month) {
            $columnSums[$month] = 0; // Initialize column sum for the month
            $report[$month] = [];

            foreach ($users as $user) {
                $deposit = $deposits[$month]->firstWhere('user_id', $user->id);
                $amount = $deposit ? $deposit->total_amount : 0;

                $report[$month][$user->id] = [
                    'user_name' => $user->name,
                    'deposited' => $deposit ? true : false,
                    'amount' => $amount,
                ];

                // Update sums
                $columnSums[$month] += $amount; // Add to column sum
                $rowSums[$user->id] += $amount; // Add to row sum
            }
        }


        // Pass data to the view
        return view('admin.transaction.monthly', compact('report', 'users', 'months', 'columnSums', 'rowSums'));

    }

}
