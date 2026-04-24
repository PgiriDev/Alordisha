<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;

class BranchController extends Controller
{
    public function index()
    {
        // Get all branches
        $branches = Branch::orderBy('id', 'desc')->get();

        foreach ($branches as $branch) {

            // COUNT STUDENTS (normal FK)
            $branch->students_count = $branch->students()->count();

            // COUNT TEACHERS (stored in JSON: users.branches)
            $branch->teachers_count = User::where('role', 'teacher')
                ->whereJsonContains('branches', (int)$branch->id)  // force integer
                ->count();
        }

        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => 'required'
        ]);

        Branch::create([
            'name' => $r->name
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $r, $id)
    {
        $branch = Branch::findOrFail($id);

        $r->validate([
            'name' => 'required'
        ]);

        $branch->update([
            'name' => $r->name
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Branch deleted successfully.');
    }
}
