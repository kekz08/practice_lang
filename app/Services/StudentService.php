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
            $baseDir = "attachments/students/{$student->StudentID}";

            // Handle Avatar
            $avatarFiles = Storage::disk('local')->files("{$baseDir}/avatar");
            $student->avatar_url = !empty($avatarFiles) ? route('students.file', [
                'id' => $student->StudentID,
                'type' => 'avatar',
                'filename' => basename($avatarFiles[0])
            ]) : null;

            // Handle Attachments
            $docFiles = Storage::disk('local')->files("{$baseDir}/docs");
            $student->attachment_urls = array_map(fn($file) => route('students.file', [
                'id' => $student->StudentID,
                'type' => 'docs',
                'filename' => basename($file)
            ]), $docFiles);

            return $student;
        });

        return $students;
    }

    public function createStudent(array $data)
    {
        $avatar = $data['avatar'] ?? null;
        $attachments = $data['attachments'] ?? [];
        unset($data['avatar'], $data['attachments']);

        $student = Student::create($data);

        // Handling path diri dapit
        Storage::disk('local')->makeDirectory("attachments/students/{$student->StudentID}/avatar");
        Storage::disk('local')->makeDirectory("attachments/students/{$student->StudentID}/docs");

        if ($avatar) {
            $this->handleAttachments($student, [$avatar], 'avatar', true);
        }

        if (!empty($attachments)) {
            $this->handleAttachments($student, $attachments, 'docs', false);
        }

        return $student;
    }

    public function updateStudent($id, array $data)
    {
        $student = Student::findOrFail($id);
        $avatar = $data['avatar'] ?? null;
        $attachments = $data['attachments'] ?? [];
        unset($data['avatar'], $data['attachments']);

        $student->update($data);

        // Handling path diri dapit
        Storage::disk('local')->makeDirectory("attachments/students/{$student->StudentID}/avatar");
        Storage::disk('local')->makeDirectory("attachments/students/{$student->StudentID}/docs");

        if ($avatar) {
            $this->handleAttachments($student, [$avatar], 'avatar', true);
        }

        if (!empty($attachments)) {
            $this->handleAttachments($student, $attachments, 'docs', false);
        }

        return $student;
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);

        // Clean up attachments directory
        $baseDir = "attachments/students/{$student->StudentID}";
        if (Storage::disk('local')->exists($baseDir)) {
            Storage::disk('local')->deleteDirectory($baseDir);
        }

        return $student->delete();
    }

    protected function handleAttachments(Student $student, array $attachments, string $subfolder = '', bool $overwrite = false)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
        $basePath = "attachments/students/{$student->StudentID}" . ($subfolder ? "/{$subfolder}" : "");

        if ($overwrite && $subfolder) {
            // Remove existing files in that specific subfolder to ensure "overwrite" behavior
            Storage::disk('local')->deleteDirectory($basePath);
            Storage::disk('local')->makeDirectory($basePath);
        }

        foreach ($attachments as $index => $base64Data) {
            if (empty($base64Data)) continue;

            if (preg_match('/^data:(\w+\/[\w\.\-]+);base64,/', $base64Data, $type)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $mimeType = $type[1]; // e.g. "image/png"
                $extension = explode('/', $mimeType)[1];
                $extension = explode('+', $extension)[0];

                // Common fixes for extensions
                $extension = match($extension) {
                    'plain' => 'txt',
                    'vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                    'vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                    'msword' => 'doc',
                    'vnd.ms-excel' => 'xls',
                    default => $extension
                };
            } else {
                $data = $base64Data;
                $extension = 'bin';
            }

            if (!in_array(strtolower($extension), $allowedExtensions) && $extension !== 'bin') {
                continue;
            }

            $data = base64_decode($data);
            if ($data === false) continue;

            // Use StudentID for avatar to overwrite, or random/indexed for docs
            if ($subfolder === 'avatar') {
                $filename = "avatar.{$extension}";
            } else {
                $filename = time() . "_{$index}.{$extension}";
            }

            $path = "{$basePath}/{$filename}";
            Storage::disk('local')->put($path, $data);
        }
    }
}
