<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $students = $this->studentService->getStudents($request);

        // fahad-select expects { results: [ { id, label } ] }
        if ($request->has('query_')) {
            $results = collect($students->items())->map(fn ($row) => [
                'id' => $row->StudentID,
                'label' => trim(($row->FirstName ?? '').' '.($row->LastName ?? '')),
            ]);

            return response()->json(['results' => $results]);
        }

        return response()->json($students);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = $this->studentService->createStudent($request->validated());

        return response()->json($student, 201);
    }

    public function update(UpdateStudentRequest $request, $id)
    {
        $student = $this->studentService->updateStudent($id, $request->validated());

        return response()->json($student);
    }

    public function destroy($id)
    {
        $this->studentService->deleteStudent($id);

        return response()->json(null, 204);
    }
}
