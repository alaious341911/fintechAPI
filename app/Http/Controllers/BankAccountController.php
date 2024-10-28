<?php
namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    public function linkBankAccount(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string|min:10|max:10',
            'bank_name' => 'required|string|max:255'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        $user = auth()->user(); 

        $bankAccount = BankAccount::create([
            'user_id' => $user->id,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
        ]);

        return response()->json(['message' => 'Bank account linked successfully', 
        'bankAccount' => $bankAccount]);
    }
}


