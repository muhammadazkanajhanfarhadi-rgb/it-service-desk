<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleFeedback extends Model
{
    protected $fillable = ['article_id', 'user_id', 'is_helpful'];

    protected $casts = ['is_helpful' => 'boolean'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(KnowledgeArticle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
