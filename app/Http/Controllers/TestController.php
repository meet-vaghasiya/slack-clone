<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use Illuminate\Http\Request;

class TestController extends Controller
{
    function index(Request $request)
    {

        MessageEvent::dispatch($request->message);
    }
}
