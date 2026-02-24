<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::orderBy('id')->get();

        return view('student.index', compact('students'));
    }

    public function create()
    {
        return view('student.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string', 'max:1000'],
        ]);

        Student::create($data);

        return redirect()->route('student.index')->with('success', 'Student added.');
    }

    public function edit(Student $student)
    {
        return view('student.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string', 'max:1000'],
        ]);

        $student->update($data);

        return redirect()->route('student.index')->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('student.index')->with('success', 'Student deleted.');
    }
}
