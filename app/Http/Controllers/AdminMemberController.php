<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);

        $query = User::query()
            ->with(['memberships' => fn($q) => $q->where('year', $year)])
            ->latest();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        return Inertia::render('Admin/Members/Index', [
            'users' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['search']),
            'year' => $year,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $member)
    {
        $year = (int) $request->input('year', now()->year);

        $member->load([
            'memberships' => fn($q) => $q->where('year', $year),
        ]);

        return Inertia::render('Admin/Members/Show', [
            'member' => $member,
            'year' => $year,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:super_admin,direzione,segreteria,member',
        ]);

        if ($validated['role'] !== UserRole::Member->value) {
            $this->authorize('manage-roles');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Default password
            'role' => $validated['role'],
            'membership_status' => 'inactive',
        ]);

        return redirect()->back()->with('success', 'Socio creato con successo.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $member)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'first_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($member->id)],
            'phone' => 'sometimes|nullable|string|max:50',

            'birth_date' => 'sometimes|nullable|date',
            'birth_place_type' => 'sometimes|nullable|string|in:it,foreign',
            'birth_province_code' => 'sometimes|nullable|string|size:2',
            'birth_city' => 'sometimes|nullable|string|max:255',
            'birth_country' => 'sometimes|nullable|string|max:255',

            'residence_type' => 'sometimes|nullable|string|in:it,foreign',
            'residence_street' => 'sometimes|nullable|string|max:255',
            'residence_house_number' => 'sometimes|nullable|string|max:50',
            'residence_locality' => 'sometimes|nullable|string|max:255',
            'residence_province_code' => 'sometimes|nullable|string|size:2',
            'residence_city' => 'sometimes|nullable|string|max:255',
            'residence_country' => 'sometimes|nullable|string|max:255',

            'plv_joined_at' => 'sometimes|nullable|date',
            // plv_expires_at is computed server-side from plv_joined_at (1 year)

            'role' => 'sometimes|required|string|in:super_admin,direzione,segreteria,member',
        ]);

        // Safety net: if migrations haven't run yet (e.g. during deploy), avoid writing to missing columns.
        // This prevents 500s like: column "first_name" of relation "users" does not exist.
        $validated = array_filter(
            $validated,
            fn($_v, $k) => Schema::hasColumn('users', $k),
            ARRAY_FILTER_USE_BOTH
        );

        if (array_key_exists('role', $validated) && $validated['role'] !== $member->role) {
            $this->authorize('manage-roles');
        }

        if (array_key_exists('plv_joined_at', $validated)) {
            if (Schema::hasColumn('users', 'plv_expires_at')) {
                $validated['plv_expires_at'] = $validated['plv_joined_at']
                    ? Carbon::parse($validated['plv_joined_at'])->addYear()->toDateString()
                    : null;
            }
        }

        $member->update($validated);

        return redirect()->back()->with('success', 'Socio aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $member)
    {
        $member->delete();

        return redirect()->back()->with('success', 'Socio eliminato con successo.');
    }
}
