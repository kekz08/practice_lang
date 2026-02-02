<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';

    protected $primaryKey = 'CourseID';

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
        'CourseCode',
        'Description',
        'Units',
        'LectureUnits',
        'LectureHours',
        'LaboratoryHours',
        'LaboratoryUnits',
        'CourseTypeID',
        'SchoolFeeTypeID',
        'created_by',
        'updated_by',
        'archived',
        'status',
        'CampusID',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'CourseTypeID' => 'integer',
        'SchoolFeeTypeID' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'archived' => 'integer',
        'CampusID' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
