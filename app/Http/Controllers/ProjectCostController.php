<?php

namespace App\Http\Controllers;

use App\Models\ProjectCost;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'type' => 'required|in:material,labor,logistics,other',
            'date' => 'required|date',
        ]);
    
        $project->costs()->create($validated);
        return redirect()->route('projects.show', $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectCost $projectCost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectCost $projectCost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectCost $projectCost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectCost $projectCost)
    {
        //
    }
}
