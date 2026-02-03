<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{

    public function index(Request $request)
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

        // fahad-select expects { results: [ { id, label } ] }
        if ($request->has('query_')) {
            $items = $query->limit(100)->get();
            $results = $items->map(fn ($row) => [
                'id' => $row->StudentID,
                'label' => trim(($row->FirstName ?? '').' '.($row->LastName ?? '')),
            ]);

            return response()->json(['results' => $results]);
        }

        $perPage = (int) $request->input('per_page', 10);

        return response()->json($query->paginate($perPage));
    }
}
