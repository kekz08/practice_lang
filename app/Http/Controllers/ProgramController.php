<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::query();

        if ($search = $request->input('search')) {
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

        $perPage = (int) $request->input('per_page', 10);

        return response()->json($query->paginate($perPage));
    }
}
