<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = \App\Models\Subject::orderBy('id', 'desc')->get();
        return view('admin.subjects.index', compact('subjects'));
    }


    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => 'required|unique:subjects,name'
        ]);

        Subject::create([
            'name' => $r->name,
        ]);

        return redirect()->route('subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $r, $id)
    {
        $subject = Subject::findOrFail($id);

        $r->validate([
            'name' => "required|unique:subjects,name,$id"
        ]);

        $subject->update([
            'name' => $r->name,
        ]);

        return redirect()->route('subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
