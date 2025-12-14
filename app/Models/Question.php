<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tank_id',
        'title',
        'content',
        'image_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}





// 10. MODEL RELATIONSHIPS (if not already defined)
// Path: app/Models/Question.php
// ============================================================================

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'user_id',
        'tank_id',
        'title',
        'content',
        'image_path',
        'status', // open, answered, resolved
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
