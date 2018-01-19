<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TokenController extends Controller
{
    public function index(Request $request)
    {
        $token = array('token' => $request->session()->token());
        return new Response($token, 200);
    }
}
