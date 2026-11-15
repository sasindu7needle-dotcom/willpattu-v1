<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seeded copy that named Makara Resorts, mapped to the Dolphin Beach Resort wording.
     * Only values still matching the original defaults are changed, so admin edits are kept.
     *
     * @var array<string, string>
     */
    private const REPLACEMENTS = [
        'Explore wildlife, safari trails and natural landscapes photographed for the Makara Wilpattu guide.' => 'Explore wildlife, safari trails and natural landscapes photographed on Wilpattu safaris from Dolphin Beach Resort.',
        '"reservations_label":"MAKARA RESORTS RESERVATIONS"' => '"reservations_label":"DOLPHIN BEACH RESORT RESERVATIONS"',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->replace(self::REPLACEMENTS);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->replace(array_flip(self::REPLACEMENTS));
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function replace(array $replacements): void
    {
        foreach (DB::table('page_contents')->get(['id', 'intro', 'settings']) as $page) {
            $intro = strtr((string) $page->intro, $replacements);
            $settings = $page->settings === null ? null : strtr($page->settings, $replacements);

            if ($intro !== (string) $page->intro || $settings !== $page->settings) {
                DB::table('page_contents')->where('id', $page->id)->update([
                    'intro' => $intro,
                    'settings' => $settings,
                ]);
            }
        }
    }
};
