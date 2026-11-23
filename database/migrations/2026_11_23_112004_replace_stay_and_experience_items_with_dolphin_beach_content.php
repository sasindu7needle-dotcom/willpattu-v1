<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Item titles seeded before this change. Pages whose items still match these
     * are replaced; pages an administrator has already edited are left untouched.
     *
     * @var array<string, list<string>>
     */
    private const PREVIOUS_TITLES = [
        'stay' => ['A retreat for two', 'Space for your family', 'Your stay, your way'],
        'experiences' => ['Birdwatching', 'Photography', 'Culture & heritage', 'Village & nature experiences'],
    ];

    /**
     * @var array<string, string>
     */
    private const PREVIOUS_INTROS = [
        'stay' => 'Slow mornings. Quiet evenings. A thoughtful base for your Wilpattu escape. Speak with our team to find the right accommodation for your dates.',
        'experiences' => 'Make room for the quieter discoveries. Tell us what you love and we will help you explore the possibilities around your stay.',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (array_keys(self::PREVIOUS_TITLES) as $slug) {
            $page = DB::table('page_contents')->where('slug', $slug)->first();

            if ($page === null || ! $this->itemsMatch($page->id, self::PREVIOUS_TITLES[$slug])) {
                continue;
            }

            $content = config('content.pages.'.$slug);

            $this->replaceItems($page->id, $content['items']);

            if ($page->intro === self::PREVIOUS_INTROS[$slug]) {
                DB::table('page_contents')->where('id', $page->id)->update(['intro' => $content['intro'], 'updated_at' => now()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The previous placeholder content is intentionally not restored.
    }

    /**
     * @param  list<string>  $titles
     */
    private function itemsMatch(int $pageId, array $titles): bool
    {
        $current = DB::table('page_content_items')
            ->where('page_content_id', $pageId)
            ->orderBy('position')
            ->pluck('title')
            ->all();

        return $current === $titles;
    }

    /**
     * @param  list<array{title: string, meta: string, text: string, image: int}>  $items
     */
    private function replaceItems(int $pageId, array $items): void
    {
        DB::table('page_content_items')->where('page_content_id', $pageId)->delete();

        foreach ($items as $position => $item) {
            DB::table('page_content_items')->insert([
                'page_content_id' => $pageId,
                'title' => $item['title'],
                'meta' => $item['meta'],
                'text' => $item['text'],
                'image' => $item['image'],
                'position' => $position,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
