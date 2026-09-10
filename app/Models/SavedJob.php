<?php

namespace App\Models;

use Database\Factories\SavedJobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedJob extends Model
{
    /** @use HasFactory<SavedJobFactory> */
    use HasFactory;
}
