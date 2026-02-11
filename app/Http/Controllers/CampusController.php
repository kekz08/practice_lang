<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Http\Requests\StoreCampusRequest;
use App\Http\Requests\UpdateCampusRequest;
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

    public function store(StoreCampusRequest $request)
    {
        $campus = Campus::create($request->validated());

        return response()->json($campus, 201);
    }

    public function update(UpdateCampusRequest $request, $id)
    {
        $campus = Campus::findOrFail($id);

        $campus->update($request->validated());

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
