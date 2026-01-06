<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MemberProfileController extends Controller
{
    public function show()
    {
        return Inertia::render('Member/Profile');
    }
}


