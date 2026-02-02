<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $table = 'classes';

    protected $primaryKey = 'ClassID';

    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ClassCode',
        'AYFrom',
        'AYTo',
        'Semester',
        'CourseID',
        'EmployeeID',
        'ProgramID',
        'CurriculumID',
        'SectionCode',
        'MaxSize',
        'Size',
        'ClassTypeID',
        'created_by',
        'updated_by',
        'status',
        'archived',
        'CurriculumCourseID',
        'GClassID',
        'CourseLink',
        'GClassIn',
        'CampusID',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'AYFrom' => 'integer',
        'AYTo' => 'integer',
        'Semester' => 'integer',
        'CourseID' => 'integer',
        'EmployeeID' => 'integer',
        'ProgramID' => 'integer',
        'CurriculumID' => 'integer',
        'MaxSize' => 'integer',
        'Size' => 'integer',
        'ClassTypeID' => 'integer',
        'archived' => 'integer',
        'CurriculumCourseID' => 'integer',
        'GClassIn' => 'integer',
        'CampusID' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
