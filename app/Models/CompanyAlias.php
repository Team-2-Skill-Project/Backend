<?php

namespace App\Models;

use Database\Factories\CompanyAliasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAlias extends Model
{
    /** @use HasFactory<CompanyAliasFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'alias',
        'normalized_alias',
    ];

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
