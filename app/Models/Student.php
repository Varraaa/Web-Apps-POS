<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi melalui form CRUD.
    protected $fillable = [
        'nis',
        'name',
        'class_name',
        'major',
        'gender',
        'birth_date',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }
}
