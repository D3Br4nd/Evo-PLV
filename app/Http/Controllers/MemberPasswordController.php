<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberPasswordController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // After first setup, require current password.
        if (! $user->must_set_password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'must_set_password' => false,
        ])->save();

        return redirect()->back()->with('success', 'Password aggiornata con successo.');
    }
}


