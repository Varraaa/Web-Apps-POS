<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // Pencarian berjalan pada NIS, nama, kelas, dan jurusan.
        $search = $request->string('search')->trim()->toString();
        $students = Student::query()
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('nis', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('class_name', 'like', "%{$search}%")
                    ->orWhere('major', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('students.index', [
            'students' => $students,
            'search' => $search,
            'totalStudents' => Student::count(),
            'activeStudents' => Student::where('status', 'Aktif')->count(),
            'alumniStudents' => Student::where('status', 'Alumni')->count(),
        ]);
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        Student::create($this->validated($request));

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $student->update($this->validated($request, $student));

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:students,nis,'.($student?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:255'],
            'class_name' => ['required', 'string', 'max:30'],
            'major' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'birth_date' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string'],
            'status' => ['required', 'in:Aktif,Alumni,Nonaktif'],
        ]);
    }
}
