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

        if ($request->has('CampusID')) {
            $query->where('CampusID', $request->input('CampusID'));
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
                'label' => trim(($row->CampusCode ?? '').' '.($row->CampusName ?? '')),
            ]);

            return response()->json(['results' => $results]);
        }

        $perPage = (int) $request->input('per_page', 10);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CampusCode' => 'required|string|max:50|unique:campus,CampusCode',
            'CampusName' => 'required|string|max:255',
            'Location' => 'nullable|string|max:255',
            'CampusHead' => 'nullable|integer',
            'OfficeCode' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        $campus = Campus::create($validated);

        return response()->json($campus, 201);
    }

    public function update(Request $request, $id)
    {
        $campus = Campus::findOrFail($id);

        $validated = $request->validate([
            'CampusCode' => 'required|string|max:50|unique:campus,CampusCode,'.$id.',CampusID',
            'CampusName' => 'required|string|max:255',
            'Location' => 'nullable|string|max:255',
            'CampusHead' => 'nullable|integer',
            'OfficeCode' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        $campus->update($validated);

        return response()->json($campus);
    }

    public function destroy($id)
    {
        $campus = Campus::findOrFail($id);
        $campus->delete();

        return response()->json(null, 204);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        Campus::whereIn('CampusID', $ids)->delete();

        return response()->json(null, 204);
    }
}
