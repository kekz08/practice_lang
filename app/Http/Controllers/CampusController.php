<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index(Request $request)
    {
        $query = Campus::query();

        $search = $request->input('search') ?? $request->input('query_');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('CampusID', 'like', "%{$search}%")
                    ->orWhere('CampusCode', 'like', "%{$search}%")
                    ->orWhere('CampusName', 'like', "%{$search}%")
                    ->orWhere('Location', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'CampusID');
        $order = $request->input('order', 'asc');
        if (in_array(strtolower($order), ['asc', 'desc'])) {
            $query->orderBy($sort, $order);
        }

        // fahad-select expects { results: [ { id, label } ] }
        if ($request->has('query_')) {
            $items = $query->limit(100)->get();
            $results = $items->map(fn ($row) => [
                'id' => $row->CampusID,
                'label' => $row->CampusName,
            ]);

            return response()->json(['results' => $results]);
        }

        $perPage = (int) $request->input('per_page', 10);

        return response()->json($query->paginate($perPage));
    }
}
