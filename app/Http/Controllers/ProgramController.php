<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::query();

        if ($request->filled('ProgramID')) {
            $query->where('ProgramID', $request->input('ProgramID'));
        }

        $search = $request->input('search') ?? $request->input('query_');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ProgramID', 'like', "%{$search}%")
                    ->orWhere('ProgramCode', 'like', "%{$search}%")
                    ->orWhere('ProgramName', 'like', "%{$search}%")
                    ->orWhere('Major', 'like', "%{$search}%")
                    ->orWhere('Status', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'ProgramID');
        $order = $request->input('order', 'asc');
        if (in_array(strtolower($order), ['asc', 'desc'])) {
            $query->orderBy($sort, $order);
        }

        // fahad-select expects { results: [ { id, label } ] }
        if ($request->has('query_')) {
            $items = $query->limit(100)->get();
            $results = $items->map(fn ($row) => ['id' => $row->ProgramID, 'label' => $row->ProgramName ?? $row->ProgramCode]);

            return response()->json(['results' => $results]);
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = min(max($perPage, 1), 1000);

        return response()->json($query->paginate($perPage));
    }
}
