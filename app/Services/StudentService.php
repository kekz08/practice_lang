<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentService
{
    public function getStudents(Request $request)
    {
        $curriculumCampusSub = DB::table('classes')
            ->join('programs', 'classes.ProgramID', '=', 'programs.ProgramID')
            ->join('campus', 'programs.CampusID', '=', 'campus.CampusID')
            ->select(
                'classes.CurriculumID',
                DB::raw('MAX(campus.CampusName) as campus_name'),
                DB::raw('MAX(programs.ProgramName) as program_name')
            )
            ->groupBy('classes.CurriculumID');

        $query = Student::query()
            ->leftJoinSub($curriculumCampusSub, 'curriculum_campus', 'students.CurriculumID', '=', 'curriculum_campus.CurriculumID')
            ->select('students.*', 'curriculum_campus.campus_name', 'curriculum_campus.program_name');

        if ($request->filled('StudentID')) {
            $query->where('students.StudentID', $request->input('StudentID'));
        }

        if ($request->filled('ProgramID')) {
            $query->whereExists(function ($q) use ($request) {
                $q->select(DB::raw(1))
                    ->from('classes')
                    ->whereColumn('classes.CurriculumID', 'students.CurriculumID')
                    ->where('classes.ProgramID', $request->input('ProgramID'));
            });
        }

        if ($request->filled('CampusID')) {
            $query->whereExists(function ($q) use ($request) {
                $q->select(DB::raw(1))
                    ->from('classes')
                    ->join('programs', 'classes.ProgramID', '=', 'programs.ProgramID')
                    ->whereColumn('classes.CurriculumID', 'students.CurriculumID')
                    ->where('programs.CampusID', $request->input('CampusID'));
            });
        }

        $search = $request->input('search') ?? $request->input('query_');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('students.StudentID', 'like', "%{$search}%")
                    ->orWhere('students.FirstName', 'like', "%{$search}%")
                    ->orWhere('students.MiddleName', 'like', "%{$search}%")
                    ->orWhere('students.LastName', 'like', "%{$search}%")
                    ->orWhere('students.Email', 'like', "%{$search}%")
                    ->orWhere('students.UserEmail', 'like', "%{$search}%")
                    ->orWhere('students.PhoneNumber', 'like', "%{$search}%")
                    ->orWhere('students.Address', 'like', "%{$search}%")
                    ->orWhere('students.status', 'like', "%{$search}%")
                    ->orWhere('curriculum_campus.campus_name', 'like', "%{$search}%")
                    ->orWhere('curriculum_campus.program_name', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'StudentID');
        $order = $request->input('order', 'asc');
        if (in_array(strtolower($order), ['asc', 'desc'])) {
            $sortColumn = match ($sort) {
                'campus_name' => 'curriculum_campus.campus_name',
                'program_name' => 'curriculum_campus.program_name',
                default => 'students.' . $sort,
            };
            $query->orderBy($sortColumn, $order);
        }

        $students = $query->paginate($request->input('per_page', 10));

        $students->getCollection()->transform(function ($student) {
            $directory = "attachments/students/{$student->StudentID}";
            $files = Storage::disk('public')->files($directory);

            $student->attachment_url = null;
            if (!empty($files)) {
                $student->attachment_url = asset('storage/' . $files[0]);
            }
            return $student;
        });

        return $students;
    }

    public function createStudent(array $data)
    {
        $attachments = $data['attachments'] ?? [];
        unset($data['attachments']);

        $student = Student::create($data);

        // Ensure the student's attachment directory exists
        Storage::disk('public')->makeDirectory("attachments/students/{$student->StudentID}");

        if (!empty($attachments)) {
            $this->handleAttachments($student, $attachments);
        }

        return $student;
    }

    public function updateStudent($id, array $data)
    {
        $student = Student::findOrFail($id);
        $attachments = $data['attachments'] ?? [];
        unset($data['attachments']);

        $student->update($data);

        Storage::disk('public')->makeDirectory("attachments/students/{$student->StudentID}");

        if (!empty($attachments)) {
            $this->handleAttachments($student, $attachments);
        }

        return $student;
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        return $student->delete();
    }

    protected function handleAttachments(Student $student, array $attachments)
    {
        foreach ($attachments as $base64Data) {
            if (empty($base64Data)) continue;

            if (preg_match('/^data:(\w+\/[\w\.\-]+);base64,/', $base64Data, $type)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $mimeType = $type[1]; // e.g. "image/png"
                $extension = explode('/', $mimeType)[1];
                $extension = explode('+', $extension)[0];
            } else {
                $data = $base64Data;
                $extension = 'bin';
            }

            $data = base64_decode($data);
            if ($data === false) continue;

            $filename = $student->StudentID . '.' . $extension;
            $path = "attachments/students/{$student->StudentID}/{$filename}";

            Storage::disk('public')->put($path, $data);
        }
    }
}
