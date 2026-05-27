<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManufacturingTeamController extends Controller
{
    /**
     * Display a listing of the manufacturing teams.
     */
    public function index()
    {
        $manufacturingTeams = ManufacturingTeam::latest()->paginate(10);
        return view('admin.manufacturing-teams.index', compact('manufacturingTeams'));
    }

    /**
     * Show the form for creating a new manufacturing team.
     */
    public function create()
    {
        return view('admin.manufacturing-teams.create');
    }

    /**
     * Store a newly created manufacturing team in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'factory_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:manufacturing_teams,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'manufacturing_unit_type' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        ManufacturingTeam::create($validatedData);

        return redirect()->route('admin.manufacturing-teams.index')
            ->with('success', 'Manufacturing team created successfully.');
    }

    /**
     * Display the specified manufacturing team.
     */
    public function show(ManufacturingTeam $manufacturingTeam)
    {
        return view('admin.manufacturing-teams.show', compact('manufacturingTeam'));
    }

    /**
     * Show the form for editing the specified manufacturing team.
     */
    public function edit(ManufacturingTeam $manufacturingTeam)
    {
        return view('admin.manufacturing-teams.edit', compact('manufacturingTeam'));
    }

    /**
     * Update the specified manufacturing team in storage.
     */
    public function update(Request $request, ManufacturingTeam $manufacturingTeam)
    {
        $validatedData = $request->validate([
            'factory_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('manufacturing_teams')->ignore($manufacturingTeam->id),
            ],
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'required|string',
            'manufacturing_unit_type' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Only include password in update if it's being changed
        if (empty($validatedData['password'])) {
            unset($validatedData['password']);
        }

        $manufacturingTeam->update($validatedData);

        return redirect()->route('admin.manufacturing-teams.index')
            ->with('success', 'Manufacturing team updated successfully.');
    }

    /**
     * Remove the specified manufacturing team from storage.
     */
    public function destroy(ManufacturingTeam $manufacturingTeam)
    {
        $manufacturingTeam->delete();

        return redirect()->route('admin.manufacturing-teams.index')
            ->with('success', 'Manufacturing team deleted successfully.');
    }
}