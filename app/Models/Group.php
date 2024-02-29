<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Group
 *
 * @property int    $id
 * @property string $name
 * @property bool   $active
 * @property string $created_at
 * @property string $updated_at
 *
 * */
class Group extends Model
{
    use HasFactory, softDeletes;

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

}
