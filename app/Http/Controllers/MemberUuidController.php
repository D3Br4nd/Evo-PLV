<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MemberUuidController extends Controller
{
    public function show()
    {
        return Inertia::render('Member/Uuid');
    }
}


