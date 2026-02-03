<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'ProgramID';

    protected $fillable = [
        'ProgramCode',
        'ProgramName',
        'UnitID',
        'Major',
        'Minor',
        'ProgramTypeID',
        'ProgramChair',
        'C1',
        'C2',
        'C3',
        'parent',
        'child',
        'order',
        'YearOffered',
        'NumberYears',
        'created_by',
        'updated_by',
        'Status',
        'archived',
        'CampusID',
        'BoardCourses',
    ];

    public $timestamps = true;
}
