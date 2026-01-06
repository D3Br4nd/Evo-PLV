<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MemberHomeController extends Controller
{
    public function show()
    {
        return Inertia::render('Member/Home');
    }
}


