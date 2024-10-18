<?php

namespace App\Http\Controllers;

use App\Models\CustomerData;
use App\Models\numberdetails;
use App\Models\NumberMaker;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    // Route::post('/login', function (Request $request) {
    public function login(Request $request){
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt to authenticate the user
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Create a token for the user
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user
            ], 200);
        } else {
            // Return an error if the login fails
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials, please try again.'
            ], 401);
        }


    }
    //
    public function upload(Request $request)
    {
        // Validate the file type
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        // Handle file upload
        if ($request->file()) {
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

            return response()->json(['success' => 'File uploaded successfully', 'file' => $filePath]);
        }

        return response()->json(['error' => 'File upload failed'], 500);
    }
    //
    public function getAllData(Request $request)
    {
        // return "zoom";
        // return $request->customerId;
        // $user = Auth::user();
        $user = Auth::user();

        $data = CustomerData::where('master_number',$request->customerId)
        ->whereNot('user_id',$user->id)
        ->get();
        // if($data)
        foreach($data as $d){
            $d = CustomerData::where('id',$d->id)
            ->whereNot('user_id',$user->id)
            ->first();
            if($d){
                $d->user_id = $user->id;
                $d->save();

            }
        }


        // Fetch all data from customer_data table
        $customers = CustomerData::where('user_id',$user->id)
        ->where('master_number',$request->customerId)
        ->where('status','No Responsed Yet')
        ->paginate(50);

        return response()->json($customers);
    }
    //
    //
    public function numberdetails()
    {
        // $user = Auth::user();
        $user = Auth::user();

        $customers = numberdetails::where('status','Available')
        ->simplePaginate(100);
        // $customers = CustomerData::where('user_id', $user->id)
        // ->where('status', 'No Responsed Yet')
        // ->paginate(50);
        // ->paginate(50);
        // Fetch all data from customer_data table
        // $customers = CustomerData::where('user_id',$user->id)
        // ->where('status','No Responsed Yet')
        // ->paginate(50);

        return response()->json($customers);
    }
    //
    //
    public function dncr_number()
    {
        // $user = Auth::user();
        $user = Auth::user();

        $customers = NumberMaker::where('status',3)
        ->where('dncr_file',0)
        ->simplePaginate(100);
        // $customers = CustomerData::where('user_id', $user->id)
        // ->where('status', 'No Responsed Yet')
        // ->paginate(50);
        // ->paginate(50);
        // Fetch all data from customer_data table
        // $customers = CustomerData::where('user_id',$user->id)
        // ->where('status','No Responsed Yet')
        // ->paginate(50);

        return response()->json($customers);
    }
    //
    public function storeFeedback(Request $request)
    {
        // return $request;
        // Validate the incoming request data
        $validatedData = $request->validate([
            'CustomerNumber' => 'required|string',
            // 'IsWhatsApp' => 'required|boolean',
            // 'IsDncr' => 'required|boolean',
            'Status' => 'required|in:Interested,Follow Up,Not Answer',
        ]);
        $customer  = CustomerData::where('id',$request->id)->first();
        $customer->status = $validatedData['Status'];
        $customer->save();
        // Create and store the customer feedback
        // $customer = CustomerData::create([
            // 'CustomerNumber' => $validatedData['CustomerNumber'],
            // 'IsWhatsApp' => $validatedData['IsWhatsApp'],
            // 'IsDncr' => $validatedData['IsDncr'],
            // 'Status' => $validatedData['Status'],
        // ]);

        return response()->json(['message' => 'Feedback submitted successfully', 'data' => $customer]);
    }

    //
    public function checkMasterData(Request $request)
    {
        // return $number;
        $user = Auth::user();

        // Check if the number exists in the master data table
        $customer = CustomerData::where('master_number', $request->number)
        ->where('user_id',$user->id)
        ->where('status', 'No Responsed Yet')
        ->first();
        if($customer){
            return response()->json(['exists' => true, 'customerId' => $customer->master_number]);
        }
        $customerUniqueness = CustomerData::where('user_id', $user->id)
        ->where('status', 'No Responsed Yet')
        ->first();
        if(!$customerUniqueness){
            $customerJD = CustomerData::where('master_number', $request->number)
                // ->where('user_id',$user->id)
                ->where('status', 'No Responsed Yet')
                ->first();
            if($customerJD){
                return response()->json(['exists' => true, 'customerId' => $customerJD->master_number]);
            }
        }
        else{
            return response()->json(['exists' => false, 'Error' => 'Please Clear Previous List']);
            // return response()->json(['exists' => false]);
        }


        return response()->json(['exists' => false]);
    }

    //
    public function getDashboardStats($userId)
    {
        $user = Auth::user();

        // Fetch all customer records assigned to the user
        $assignedData = CustomerData::where('user_id', $user->id)->get();

        // Total Assigned Data
        $totalAssignedData = $assignedData->count();

        // Calculate the status counts
        $interestedCount = $assignedData->where('status', 'Interested')->count();
        $followUpCount = $assignedData->where('status', 'Follow Up')->count();
        $noAnswerCount = $assignedData->where('status', 'No Answer')->count();

        // Total Remaining Data (unprocessed records)
        $remainingData = $assignedData->where('status', 'No Responsed Yet')->count();

        // Return all stats in a response
        return response()->json([
            'totalAssignedData' => $totalAssignedData,
            'interestedCount' => $interestedCount,
            'followUpCount' => $followUpCount,
            'noAnswerCount' => $noAnswerCount,
            'remainingData' => $remainingData
        ]);
    }
    //
    public function uploadNumberFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'number' => 'required|string'
        ]);

        // Store the uploaded file
        $file = $request->file('file')->store('uploads');

        return response()->json(['message' => 'File uploaded successfully']);
    }
    //
    public function AvailableNumber(Request $request)
    {
        $customers = numberdetails::where('status', 'Available')
        ->paginate(10);
        // $customers = CustomerData::where('user_id', $user->id)
        // ->where('status', 'No Responsed Yet')
        // ->paginate(50);
        // ->paginate(50);
        // Fetch all data from customer_data table
        // $customers = CustomerData::where('user_id',$user->id)
        // ->where('status','No Responsed Yet')
        // ->paginate(50);

        return response()->json($customers);
    }
    //
    public function UploadNumber(Request $request){
        // return $request->number;
        $user = Auth::user();
        // $d = NumberMaker::where('status',1)->first();
        $a = NumberMaker::where('status',3)->where('number',$request->number)->first();
        $c = NumberMaker::where('status',1)->where('number',$request->number)->first();
        if($c){

            return response()->json(['exists' => true, 'customerId' => $c->number]);
        }
        else if($a){
            return response()->json(['exists' => false, 'Error' => 'Already Clear']);

        }
        // else if($d){
        //     return response()->json(['exists' => false, 'Error' => 'Already Exist, Please Clear Previous Pool']);

        // }
        $number = NumberMaker::create([
            'number' => $request->number,
            'user_id'=> $user->id,
            'number_id' => $request->number_id,
            'status' => 1
        ]);

        if ($number) {
            return response()->json(['exists' => true, 'customerId' => $number->number]);
        }
    }
    public function UploadMasterDataDNCR(Request $request){
        // return $request->number;
        $user = Auth::user();
        // $d = NumberMaker::where('status',1)->first();
        $a = NumberMaker::where('status',3)->where('number',$request->number)->first();
        // $c = NumberMaker::where('status',1)->where('number',$request->number)->first();
        if($a){

            return response()->json(['exists' => true, 'customerId' => $a->number]);
        }

    }




}
