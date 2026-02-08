<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    /**
     * Returns curricula for FahadSelect: id = CurriculumID, label = Program Name.
     */
    public function index(Request $request)
    {
        $query = DB::table('classes')
            ->join('programs', 'classes.ProgramID', '=', 'programs.ProgramID')
            ->select('classes.CurriculumID', DB::raw('MAX(programs.ProgramName) as program_name'))
            ->groupBy('classes.CurriculumID');

        if ($request->has('query_')) {
            $search = $request->input('query_');
            if ($search) {
                $query->where('programs.ProgramName', 'like', "%{$search}%");
            }
            $items = $query->limit(100)->get();
            $results = $items->map(fn ($row) => [
                'id' => $row->CurriculumID,
                'label' => $row->program_name ?? "Curriculum {$row->CurriculumID}",
            ]);

            return response()->json(['results' => $results]);
        }

        return response()->json(['results' => []]);
    }
}
