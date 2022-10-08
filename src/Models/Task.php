<?php

namespace Spork\Planning\Models;

use App\Models\FeatureList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Tags\HasTags;

class Task extends Model
{
    use HasFactory, HasTags, Searchable;

    protected $fillable = [
        'title', 
        'description', 
        'order', 
        'status_id',
        'feature_list_id',
        'completed_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function list()
    {
        return $this->belongsTo(FeatureList::class);
    }
}
