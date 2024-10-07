<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageAddons extends Controller
{
    public function faq(Request $request){

        return view('faq');
    }
    public function terms()
    {
        // Optionally, you can pass dynamic data to the view
        $lastUpdated = now()->format('F d, Y');

        return view('terms', compact('lastUpdated'));
    }
}
