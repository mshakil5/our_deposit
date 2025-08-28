<?php

namespace App\Http\Controllers;

use App\Models\DiscussHistory;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class DiscussController extends Controller
{

    public function getAllDiscussion()
    {
        $discussions = Discussion::where('status', 0)->orderby('id', 'DESC')->get();
        return view('user.alldiscussion', compact('discussions'));
    } 
    
    
    public function getMyDiscussion()
    {
        $users = User::where('status', 1)->get();
        $discussions = Discussion::where('user_id', Auth::user()->id)->where('status', 0)->orderby('id', 'DESC')->get();
        return view('user.discussion', compact('discussions','users'));
    } 
    

    public function discussionStore(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $data = new Discussion();
        $data->user_id = Auth::user()->id;
        $data->date = $request->date;
        $data->description = $request->description;
        $data->summery = $request->summery;
        $data->member = $request->member;
        $data->note = $request->note;

        if ($request->hasFile('document')) {

            $image = $request->file('document');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/document'), $imageName);
            $data->document = '/images/document/' . $imageName;
        }
        if ($data->save()) {
            return back()->with('success', 'Data saved successfully');
        } else {
            return back()->with('error', 'Your internet connection error. Please try again later.');
        }
        

    }

    public function discussionDelete($id)
    {

        $data = Discussion::findOrFail($id);
        $data->delete();
        return back()->with('deletesuccess', 'Your data has been delete successfully');
    } 




    public function discussionEdit($id)
    {
        $discussions = Discussion::where('id', $id)->first();

        return view('user.discussionEdit', compact('discussions'));
    } 

    public function discussionUpdate(Request $request)
    {

        
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $data = Discussion::find($request->id);

        if ($data) {
            $history = new DiscussHistory();
            $history->discussion_id = $data->id;
            $history->user_id = Auth::user()->id;
            $history->date = $data->date;
            $history->description = $data->description;
            $history->summery = $data->summery;
            $history->member = $data->member;
            $history->note = $data->note;
            $history->document = $data->document;
            $history->save();
        }

        $data->description = $request->description;
        $data->summery = $request->summery;
        $data->member = $request->member;
        $data->note = $request->note;

        if ($request->hasFile('document')) {

            $image = $request->file('document');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/document'), $imageName);
            $data->document = '/images/document/' . $imageName;
        }
        if ($data->save()) {
            return back()->with('success', 'Data saved successfully');
        } else {
            return back()->with('error', 'Your internet connection error. Please try again later.');
        }
        

    }



}
