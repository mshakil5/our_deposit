<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{


    
    public function getData($id = null)
    {
        $data = Transaction::with('user')
                    ->orderBy('id', 'DESC')
                    ->when($id, function($q) use ($id){
                        return $q->where('user_id', $id);
                    });

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('username', function($row){
                return $row->user->name . "<br>" . $row->user->phone;
            })
            ->addColumn('document', function($row){
                return '<a href="'.asset($row->document).'" target="_blank">
                            <img src="'.asset($row->document).'"
                                style="max-width:100px;height:auto;">
                        </a>';
            })
            ->addColumn('status_switch', function($row){
                $checked = $row->status == 1 ? 'checked' : '';
                return '
                <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input toggle-status"
                        id="switch'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                <label class="custom-control-label" for="switch'.$row->id.'"></label>
                </div>
                ';
            })
            ->rawColumns(['username','document','status_switch']) // IMPORTANT
            ->make(true);
    }

    public function index($id = null)
    {
        return view('admin.transaction.index', compact('id'));
    }


    public function pending()
    {
        $data = Transaction::orderby('id', 'DESC')->where('status', 0)->get();
        return view('admin.transaction.pending', compact('data'));
    }

    public function indexold($id = null)
    {
        $data = Transaction::orderby('id', 'DESC')
        ->when($id, function($query) use ($id) {
            return $query->where('user_id', $id);
        })
        ->where('status', 1)->get();
        return view('admin.transaction.index', compact('data'));
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
        $data->note = $request->note;
        $data->tran_type = $data->tran_type ?? 'Deposit'; // Ensure type is set
        $data->payment_type = $data->payment_type ?? 'Bank';
        
        // Logic for Fine: Create new row if fine is provided
        if ($request->fine > 0) {
            $fine = new Transaction();
            $fine->user_id = $data->user_id;
            $fine->tranid = $data->tranid . '-' . rand(10, 99); // Unique ID
            $fine->date = $request->date;
            $fine->amount = $request->fine; // Fine value goes to 'amount' column
            $fine->fine = 0; // Keep fine column 0
            $fine->tran_type = 'Fine';
            $fine->payment_type = 'Bank';
            $fine->note = "Fine added to transaction " . $data->tranid;
            $fine->save();
        }

        $data->fine = 0; // Always keep the main record's fine column 0
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data updated and Fine processed.']);
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
            ->groupBy('month', 'user_id')->where('status', 1)
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

    public function newAccountHistory()
    {

        $users = User::where('is_type', '0')->where('status', 1)->orderby('id','DESC')->get();

        // Fetch deposits grouped by month and user
        $deposits = Transaction::select(
            DB::raw('DATE_FORMAT(STR_TO_DATE(date, "%Y-%m-%d"), "%Y-%m") as month'),
            'user_id',
            DB::raw('SUM(amount) as total_amount')
        )
        ->where('status', 1)
        ->whereDate('date', '>', '2025-05-31') // filter after May 2025
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


    public function migrateOldFines()
    {
        
        try {
            DB::transaction(function () {

                // 2. Find all records where fine is still sitting in the column
                $transactionsWithFines = Transaction::where('fine', '>', 0)->get();

                foreach ($transactionsWithFines as $original) {
                    // Create the new separate Fine row
                    $fineRow = new Transaction();
                    $fineRow->user_id      = $original->user_id;
                    $fineRow->tranid       = $original->tranid . rand(10, 99). 'F';
                    $fineRow->date         = $original->date;
                    $fineRow->amount       = $original->fine; // Move fine value to amount
                    $fineRow->document     = $original->document; // Move fine value to amount
                    $fineRow->fine         = 0;
                    $fineRow->tran_type    = 'Fine';
                    $fineRow->payment_type = 'Bank';
                    $fineRow->note         = 'Auto-generated fine from trans: ' . $original->tranid;
                    $fineRow->save();

                    // 3. Reset the fine on the original row
                    $original->fine = 0;
                    $original->save();
                }
            });

            return "Migration successful! All fines converted to individual rows.";

        } catch (\Exception $e) {
            return "Error during migration: " . $e->getMessage();
        }
    }


}
