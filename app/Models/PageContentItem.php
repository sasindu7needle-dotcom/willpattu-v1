<?php

namespace App\Models;

use Database\Factories\PageContentItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'meta', 'text', 'image', 'position'])]
class PageContentItem extends Model
{
    /** @use HasFactory<PageContentItemFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<PageContent, $this>
     */
    public function pageContent(): BelongsTo
    {
        return $this->belongsTo(PageContent::class);
    }
}
