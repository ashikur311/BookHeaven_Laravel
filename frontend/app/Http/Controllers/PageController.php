<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('about');
    }
    
    public function contact()
    {
        return view('contact');
    }
    public function faq()
{
    return view('faq');
}

public function shipping()
{
    return view('shipping');
}

public function privacy()
{
    return view('privacy');
}


}
