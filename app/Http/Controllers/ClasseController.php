<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;

class ClasseController extends Controller
{

    public function index(Request $request)
    {
        $query = Classe::query();


        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ClassCode', 'like', "%{$search}%")
                    ->orWhere('SectionCode', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('GClassID', 'like', "%{$search}%")
                    ->orWhere('CourseLink', 'like', "%{$search}%");
            });
        }


        $sort = $request->input('sort', 'ClassID');
        $order = $request->input('order', 'asc');
        if (in_array(strtolower($order), ['asc', 'desc'])) {
            $query->orderBy($sort, $order);
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = min(max($perPage, 1), 100);

        return response()->json($query->paginate($perPage));
    }
}
