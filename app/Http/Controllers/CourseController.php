<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()
            ->leftJoin('campus', 'courses.CampusID', '=', 'campus.CampusID')
            ->select('courses.*', 'campus.CampusName as campus_name');

        if ($request->filled('CourseID')) {
            $query->where('courses.CourseID', $request->input('CourseID'));
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('courses.CourseID', 'like', "%{$search}%")
                    ->orWhere('courses.CourseCode', 'like', "%{$search}%")
                    ->orWhere('courses.Description', 'like', "%{$search}%")
                    ->orWhere('courses.status', 'like', "%{$search}%")
                    ->orWhere('campus.CampusName', 'like', "%{$search}%");
            });
        }

        if ($request-> has('CourseID')) {
            $query->where('courses.CourseID', $request->input('CourseID'));
        }

        $sort = $request->input('sort', 'CourseID');
        $order = $request->input('order', 'asc');
        if (in_array(strtolower($order), ['asc', 'desc'])) {
            $sortColumn = $sort === 'campus_name' ? 'campus.CampusName' : 'courses.' . $sort;
            $query->orderBy($sortColumn, $order);
        }

        // fahad-select expects { results: [ { id, label } ] }
        if ($request->has('query_')) {
            $search = $request->input('query_');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('courses.CourseCode', 'like', "%{$search}%")
                        ->orWhere('courses.Description', 'like', "%{$search}%")
                        ->orWhere('campus.CampusName', 'like', "%{$search}%");
                });
            }
            $items = $query->limit(100)->get();
            $results = $items->map(fn ($row) => ['id' => $row->CourseID, 'label' => $row->CourseCode.($row->Description ? ' - '.$row->Description : '')]);

            return response()->json(['results' => $results]);
        }

        $perPage = (int) $request->input('per_page', 10);

        return response()->json($query->paginate($perPage));
    }
}
