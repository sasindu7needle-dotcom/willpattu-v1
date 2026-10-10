<?php

namespace App\Models;

use Database\Factories\PageContentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'title', 'kicker', 'intro', 'hero_image', 'settings'])]
class PageContent extends Model
{
    /** @use HasFactory<PageContentFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    /**
     * @return HasMany<PageContentItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(PageContentItem::class)->orderBy('position')->orderBy('id');
    }

    public static function firstOrCreateFromConfiguration(string $slug): self
    {
        $page = $slug === 'home'
            ? config('content.home')
            : config('content.pages.'.$slug);
        abort_unless($page, 404);

        $pageContent = self::query()->firstOrCreate([
            'slug' => $slug,
        ], [
            'title' => $page['title'],
            'kicker' => $page['kicker'],
            'intro' => $page['intro'],
            'hero_image' => $page['image'],
            'settings' => $page['settings'] ?? null,
        ]);

        if ($pageContent->wasRecentlyCreated) {
            foreach ($page['items'] ?? [] as $position => $item) {
                $pageContent->items()->create([...$item, 'position' => $position]);
            }
        }

        return $pageContent;
    }
}
