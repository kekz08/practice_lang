<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    protected $primaryKey = 'StudentID';

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
        'StudentYear',
        'StudentID2',
        'FirstName',
        'MiddleName',
        'LastName',
        'NameExtension',
        'BirthDate',
        'Address',
        'PhoneNumber',
        'Gender',
        'CivilStatus',
        'Citizenship',
        'Email',
        'UserEmail',
        'StudentTypeID',
        'CurriculumID',
        'YearLevel',
        'created_by',
        'updated_by',
        'status',
        'archived',
        'studentInfoID',
        'hregion',
        'hprovince',
        'hcity',
        'hbarangay',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'StudentYear' => 'integer',
        'StudentID2' => 'integer',
        'BirthDate' => 'date',
        'StudentTypeID' => 'integer',
        'CurriculumID' => 'integer',
        'YearLevel' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'archived' => 'integer',
        'studentInfoID' => 'integer',
        'hregion' => 'integer',
        'hprovince' => 'integer',
        'hcity' => 'integer',
        'hbarangay' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
