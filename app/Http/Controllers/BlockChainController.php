<?php

namespace App\Http\Controllers;

use App\Models\VoteList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class BlockChainController
 * @author TxChrixo
 * The BlockChain Controller to show the balance of the bitcoin addresses or pay the voting fees
 * @package Controllers\EA
 */
class BlockChainController extends Controller
{
    /**
     * VoteList page
     *
     * @return View
     */
    public function getBalanceList() : View
    {
        $result = VoteList::all();
        return view('ea.balance', ['data' => $result]);
    }

    /**
     * Add a Vote Page
     * 
     * @return View
     */
    public function getFeeList() : View
    {
        $result = VoteList::all();
        $user = Auth::user();
        return view('ea.fee', ['data' => $result, 'user' => $user]);
    }
}
