<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::latest()->paginate(10);
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'courseName' => 'required|string|max:255',
            'courseCode' => 'required|string|unique:courses,courseCode',
            'creditHour' => 'required|integer|min:1',
        ]);

        Course::create($request->all());

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'courseName' => 'required|string|max:255',
            'courseCode' => 'required|string|unique:courses,courseCode,' . $course->id,
            'creditHour' => 'required|integer|min:1',
        ]);

        $course->update($request->all());

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

    // Q9: enroll (attach/sync)
    public function enroll(Request $request, Student $student)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        // attach (safe because we also added unique constraint in pivot)
        $student->courses()->syncWithoutDetaching([$data['course_id']]);

        return back()->with('success', 'Course enrolled.');
    }

    // Q9: remove enrollment (detach)
    public function drop(Student $student, Course $course)
    {
        $student->courses()->detach($course->id);
        return back()->with('success', 'Course removed.');
    }
}
