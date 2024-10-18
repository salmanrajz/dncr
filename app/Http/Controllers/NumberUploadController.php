<?php

namespace App\Http\Controllers;

use App\Mail\NumbersUploadedMail;
use App\Models\CustomerData;
use App\Models\NumberMaker;
use Illuminate\Http\Request;
use App\Models\UploadedNumber;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use League\Csv\Writer; // Use this for CSV writing
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use League\Csv\Reader;



class NumberUploadController extends Controller
{
    //
    // Allowed prefixes
    protected $prefixes = ['050', '052', '056', '055', '058', '054'];
    //
    public function uploadNumbers(Request $request)
    {
        // Validate the request to ensure 'numbers' input is provided as a string
        $validator = Validator::make($request->all(), [
            'numbers' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Get the numbers input from the request
        $numbersInput = $request->input('numbers');

        // Ensure numbersInput is a string in case it's somehow sent as an array
        if (is_array($numbersInput)) {
            $numbersInput = implode(' ', $numbersInput); // Join the array into a single string
        }

        // Split the numbers by commas, newlines, or spaces
        $numbers = preg_split('/[\s,]+/', $numbersInput);

        // Filter and clean the array of numbers (remove empty values and ensure length is 7)
        $numbers = array_filter($numbers, function ($num) {
            return strlen(trim($num)) == 7; // Only accept numbers that are exactly 7 digits long
        });

        if (empty($numbers)) {
            return response()->json(['error' => 'No valid 7-digit numbers found in the input'], 400);
        }

        // Prepare numbers with prefixes
        $insertData = [];
        foreach ($numbers as $number) {
            foreach ($this->prefixes as $prefix) {
                $fullNumber = $prefix . $number; // Concatenate prefix with the 7-digit number

                // Check if the number already exists in the database
                $existing = CustomerData::where('number', $fullNumber)
                ->where('master_number',$request->MasterNumber)
                ->exists();

                if (!$existing) {
                    $insertData[] = [
                        'number' => $fullNumber,
                        'master_number' => $request->MasterNumber // Master number is the original number
                    ];
                }
            }
        }

        if (empty($insertData)) {
            return response()->json(['error' => 'All numbers already exist in the database.'], 400);
        }

        // Insert the unique numbers into the database
        CustomerData::insert($insertData);
        $z = NumberMaker::where('number',$request->MasterNumber)->first();
        $z->status = 3;
        $z->save();
        //
        // Generate CSV file in public/uploads
        $csvFileName = $request->MasterNumber . '_uploaded_numbers_' . time() . '.csv';
        $csvFilePath = public_path('uploads/' . $csvFileName); // Save in 'public/uploads/'

        // Make sure the directory exists
        if (!File::exists(public_path('uploads'))) {
            File::makeDirectory(public_path('uploads'), 0755, true); // Create the directory if it doesn't exist
        }

        // Write the CSV file
        $csv = Writer::createFromPath($csvFilePath, 'w+');
        $csv->insertOne(['Full Number',
            // 'Master Number',
            // 'User ID'
        ]); // CSV headers

        // Add rows to the CSV
        foreach ($insertData as $data) {
            $csv->insertOne(['971' .substr($data['number'], 1)]);
        }
// return $csvFilePath;
        $email = 'parhakooo@gmail.com';
        // Send email with the CSV attached
        Mail::to($email)->send(new NumbersUploadedMail($csvFilePath, $csvFileName));


        return response()->json(['success' => 'Unique numbers uploaded successfully!']);
    }
    public function UploadDNCR(Request $request)
    {
        // Validate the request to ensure 'numbers' input is provided as a string
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        //
        // Store the uploaded file in the storage temporarily
        $path = $request->file('file')->store('uploads', 'public');

        // Read the CSV file
        $csv = Reader::createFromPath(public_path('storage/' . $path), 'r');
        $csv->setHeaderOffset(0); // Assuming the CSV file has a header row

        // Initialize an array to keep track of updated records
        $updatedRecords = [];

        // Iterate through the rows in the CSV and update the database
        foreach ($csv as $record) {
            $col1 = $record['MSISDN']; // Replace 'col1' with your actual column name
            $col2 = $record['DNCR_FLAG']; // Replace 'col2' with your actual column name
            $newCol1 = '0' . substr($col1, 3);
            if($col2 == 'Y'){
                $value = 1;
            }
            else{
                $value = 0;
            }

            // return $newCol1;
            // Update the database based on the content of the CSV
            $updated = CustomerData::where('number', $newCol1)
            // ->first();
            ->update(['isDncr' => $value]);

            // Keep track of updated records
            if ($updated) {
                $updatedRecords[] = [
                    'MSISDN' => $col1,
                    'DNCR_FLAG' => $col2
                ];
            }
        }

        // Optionally, delete the file after processing (if you don't need it anymore)
        Storage::disk('public')->delete($path);
        $nk = NumberMaker::where('number',$request->customerId)->first();
        $nk->dncr_file = 1;
        $nk->save();
        // Return the list of updated records or success message
        return response()->json([
            'success' => 'Database updated successfully!',
            'updatedRecords' => $updatedRecords
        ]);



    }




}
