<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('content_blocks')
            ->select(['id', 'draft_data', 'published_data'])
            ->orderBy('id')
            ->each(function (object $block): void {
                $draftData = $this->normalizeUrls($block->draft_data);
                $publishedData = $this->normalizeUrls($block->published_data);

                if ($draftData === $block->draft_data && $publishedData === $block->published_data) {
                    return;
                }

                DB::table('content_blocks')
                    ->where('id', $block->id)
                    ->update([
                        'draft_data' => $draftData,
                        'published_data' => $publishedData,
                    ]);
            });
    }

    public function down(): void
    {
        // Relative media URLs are intentionally not converted back to a host-specific URL.
    }

    private function normalizeUrls(?string $json): ?string
    {
        if ($json === null) {
            return null;
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $normalized = $this->normalizeValue($data);

        return json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->normalizeValue($item);
            }

            return $value;
        }

        if (is_string($value)) {
            return preg_replace(
                '#^https?://[^/]+(?::\d+)?/storage/#',
                '/storage/',
                $value,
            );
        }

        return $value;
    }
};
