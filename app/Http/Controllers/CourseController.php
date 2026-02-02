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

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('courses.CourseID', 'like', "%{$search}%")
                    ->orWhere('courses.CourseCode', 'like', "%{$search}%")
                    ->orWhere('courses.Description', 'like', "%{$search}%")
                    ->orWhere('courses.status', 'like', "%{$search}%")
                    ->orWhere('campus.CampusName', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'CourseID');
        $order = $request->input('order', 'asc');
        if (in_array(strtolower($order), ['asc', 'desc'])) {
            $sortColumn = $sort === 'campus_name' ? 'campus.CampusName' : 'courses.' . $sort;
            $query->orderBy($sortColumn, $order);
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = min(max($perPage, 1), 100);

        return response()->json($query->paginate($perPage));
    }
}
