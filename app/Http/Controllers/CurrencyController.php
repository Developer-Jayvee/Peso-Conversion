<?php

namespace App\Http\Controllers;

use App\Services\ConvertServices;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{

    protected ConvertServices $convertService;

    public function __construct() {
        $this->convertService = new ConvertServices;
    }
    public function index()
    {
        return view('welcome');
    }
    public function convertCurrency(Request $request)
    {
        try {
            $validate = $request->validate([
                'amount' => 'required',
                'toString' => 'sometimes|boolean'
            ]);
            $toString = $validate['toString'] ?? false;
            $amount = trim($validate['amount']);

            return back()->with([
                'currency' => 'USD',
                'amount' => $this->convertService->convertAmount($amount,$toString)
            ]);
        } catch (\Throwable $th) {
            return back()->withErrors([
                'message' => $th->getMessage()
            ]);
        }
    }
}
