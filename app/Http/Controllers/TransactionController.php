<?php 
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function deposit(Request $request)
    {
        $validated = $request->validate(['amount' => 'required|numeric|min:1']);

        $validator = Validator::make($request->all(), [
            ['amount' => 'required|numeric|min:1']
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $user->balance += $request->amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $request->amount,
            ]);

            DB::commit();
            return response()->json(['message' => 'Deposit successful']);
        } catch (\Exception $e) {
            DB::rollBack();
            print($e);
            return response()->json(['error' => 'Transaction failed'], 500);
        }
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ['amount' => 'required|numeric|min:1']
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        $user = auth()->user();
        if ($user->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        DB::beginTransaction();
        try {
            $user->balance -= $request->amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'amount' => $request->amount,
            ]);

            DB::commit();
            return response()->json(['message' => 'Withdrawal successful']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transaction failed'], 500);
        }
    }

    public function history() {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)->get();
        return response()->json($transactions);
    }

     /**
     * Get the total count of deposits and withdrawals for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionSummary()
    {
        $user = auth()->user();

        // Count total deposits for the authenticated user
        $totalDeposits = Transaction::where('user_id', $user->id)
                                    ->where('type', 'deposit')
                                    ->count();

        // Count total withdrawals for the authenticated user
        $totalWithdrawals = Transaction::where('user_id', $user->id)
                                       ->where('type', 'withdraw')
                                       ->count();

        // Return a JSON response with the counts
        return response()->json([
            'deposit' => $totalDeposits,
            'withdraw' => $totalWithdrawals,
        ]);
    }
    
}
