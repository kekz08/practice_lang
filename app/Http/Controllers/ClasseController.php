<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;

class ClasseController extends Controller
{

    public function index(Request $request)
    {
        $query = Classe::query();

        if ($request->filled('ClassID')) {
            $query->where('ClassID', $request->input('ClassID'));
        }

        $search = $request->input('search') ?? $request->input('query_');
        if ($search) {
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

        // fahad-select expects { results: [ { id, label } ] }
        if ($request->has('query_')) {
            $items = $query->limit(100)->get();
            $results = $items->map(fn ($row) => ['id' => $row->ClassID, 'label' => $row->ClassCode]);

            return response()->json(['results' => $results]);
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = min(max($perPage, 1), 1000);

        return response()->json($query->paginate($perPage));
    }
}
