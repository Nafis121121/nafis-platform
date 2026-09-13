<?php

namespace App\Services;

use App\Enums\AiProductDraftStatus;
use App\Models\{AiProductDraft, Brand, Category, Product, ProductImage};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class AiProductImporterService
{
    public function sanitizeUtf8(mixed $data): mixed
    {
        if (is_string($data)) {
            if (! mb_check_encoding($data, 'UTF-8')) {
                $converted = @mb_convert_encoding($data, 'UTF-8', 'GB18030, GBK, GB2312, BIG5, Windows-1256, Windows-1252, ISO-8859-1');
                if (is_string($converted) && mb_check_encoding($converted, 'UTF-8')) {
                    $data = $converted;
                }
            }
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }

        if (is_array($data)) {
            return array_map([$this, 'sanitizeUtf8'], $data);
        }

        return $data;
    }

    public function importFromUrl(string $sourceUrl, ?string $rawText = null, ?int $createdBy = null): AiProductDraft
    {
        $sourceUrl = trim($this->sanitizeUtf8($sourceUrl));
        $rawText = $rawText !== null ? $this->sanitizeUtf8($rawText) : null;

        if (! filter_var($sourceUrl, FILTER_VALIDATE_URL)) {
            if (filled($sourceUrl)) {
                // If user entered a product name/model instead of a full URL
                if (blank($rawText)) {
                    $rawText = "نام و مدل کالای مورد نظر: " . $sourceUrl;
                }
                $query = urlencode($sourceUrl);
                $sourceUrl = "https://www.alibaba.com/trade/search?SearchText={$query}";
            } else {
                throw new RuntimeException('لطفاً آدرس لینک یا نام و مدل کالا را وارد نمایید.');
            }
        }

        $source = $this->fetchSource($sourceUrl);
        $payload = $this->sanitizeUtf8($this->extractProductData($sourceUrl, $source, $rawText));
        $name = $payload['name'] ?? $this->extractSlugName($sourceUrl);

        return AiProductDraft::create([
            'source_url' => $sourceUrl,
            'raw_payload' => $payload,
            'status' => AiProductDraftStatus::PENDING,
            'name' => $name ?: 'پیش‌نویس محصول',
            'name_en' => $payload['name_en'] ?? $name,
            'brand_name' => $payload['brand_name'] ?? null,
            'sku' => $payload['sku'] ?? null,
            'model_number' => $payload['model_number'] ?? null,
            'country_of_origin' => $payload['country_of_origin'] ?? null,
            'short_desc' => $payload['short_desc'] ?? null,
            'long_desc' => $payload['long_desc'] ?? null,
            'specifications' => $payload['specifications'] ?? [],
            'images' => $payload['images'] ?? [],
            'seo_title' => $payload['seo_title'] ?? $name,
            'seo_slug' => $payload['seo_slug'] ?? Str::slug($name),
            'seo_desc' => $payload['seo_desc'] ?? $payload['short_desc'] ?? null,
            'created_by' => $createdBy,
        ]);
    }

    public function approveDraftToProduct(AiProductDraft $draft, ?int $reviewedBy = null): Product
    {
        return DB::transaction(function () use ($draft, $reviewedBy): Product {
            $categoryId = $draft->category_id;
            if (empty($categoryId) || ! Category::where('id', $categoryId)->exists()) {
                $defaultCategory = Category::firstOrCreate(
                    ['slug' => 'general'],
                    ['name_fa' => 'دسته‌بندی عمومی', 'name_en' => 'General', 'is_active' => true]
                );
                $categoryId = $defaultCategory->id;
                $draft->update(['category_id' => $categoryId]);
            }

            $brand = null;
            if (filled($draft->brand_name)) {
                $brand = Brand::firstOrCreate(
                    ['slug' => Str::slug($draft->brand_name)],
                    ['name_fa' => $draft->brand_name, 'name_en' => $draft->brand_name, 'is_active' => true],
                );
            }

            $slug = $draft->seo_slug ?: $draft->sku ?: $draft->name;
            $slug = Str::slug($slug);
            $baseSlug = $slug;
            $counter = 2;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $product = Product::create([
                'category_id' => $categoryId,
                'brand_id' => $brand?->id,
                'name_fa' => $draft->name ?: $draft->name_en ?: 'محصول واردشده',
                'name_en' => $draft->name_en,
                'slug' => $slug,
                'base_sku' => $draft->sku,
                'description_fa' => $draft->long_desc ?: $draft->short_desc,
                'description_en' => $draft->long_desc,
                'metadata' => ['model_number' => $draft->model_number, 'country_of_origin' => $draft->country_of_origin, 'specifications' => $draft->specifications, 'images' => $draft->images],
                'status' => 'draft',
                'catalog_visibility' => 'hidden',
                'seo_title' => $draft->seo_title,
                'seo_slug' => $draft->seo_slug ?: $slug,
                'seo_desc' => $draft->seo_desc,
                'image_alt' => $draft->name,
                'is_active' => false,
            ]);

            $this->syncImages($product, $draft->images ?? [], $draft->name);
            $draft->update(['product_id' => $product->id, 'reviewed_by' => $reviewedBy, 'status' => AiProductDraftStatus::IMPORTED]);

            return $product;
        });
    }

    public function linkDraftToProduct(AiProductDraft $draft, Product $product, ?int $reviewedBy = null): Product
    {
        return DB::transaction(function () use ($draft, $product, $reviewedBy): Product {
            $brand = $this->resolveBrand($draft->brand_name);
            $product->update([
                'brand_id' => $brand?->id ?? $product->brand_id,
                'name_fa' => $draft->name ?: $product->name_fa,
                'name_en' => $draft->name_en ?: $product->name_en,
                'base_sku' => $draft->sku ?: $product->base_sku,
                'description_fa' => $draft->long_desc ?: $draft->short_desc ?: $product->description_fa,
                'seo_title' => $draft->seo_title ?: $product->seo_title,
                'seo_slug' => $draft->seo_slug ?: $product->seo_slug,
                'seo_desc' => $draft->seo_desc ?: $product->seo_desc,
                'image_alt' => $draft->name ?: $product->image_alt,
                'metadata' => array_merge($product->metadata ?? [], [
                    'model_number' => $draft->model_number,
                    'country_of_origin' => $draft->country_of_origin,
                    'specifications' => $draft->specifications,
                ]),
            ]);
            $this->syncImages($product, $draft->images ?? [], $draft->name);
            $draft->update(['product_id' => $product->id, 'reviewed_by' => $reviewedBy, 'status' => AiProductDraftStatus::IMPORTED]);

            return $product->refresh();
        });
    }

    private function resolveBrand(?string $brandName): ?Brand
    {
        if (blank($brandName)) {
            return null;
        }

        return Brand::firstOrCreate(
            ['slug' => Str::slug($brandName)],
            ['name_fa' => $brandName, 'name_en' => $brandName, 'is_active' => true],
        );
    }

    public function syncImages(Product $product, array $images, ?string $alt, bool $downloadLocally = false): void
    {
        $product->images()->delete();
        $validImages = array_values(array_filter($images, fn ($image): bool => is_string($image) && (
            filter_var($image, FILTER_VALIDATE_URL) || str_starts_with($image, '//') || str_starts_with($image, 'products/')
        )));

        foreach ($validImages as $index => $image) {
            $imagePath = $image;
            if (str_starts_with($imagePath, '//')) {
                $imagePath = 'https:' . $imagePath;
            }

            if ($downloadLocally && filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $localPath = $this->downloadImageToStorage($imagePath);
                if ($localPath) {
                    $imagePath = $localPath;
                }
            }

            ProductImage::create([
                'product_id' => $product->id,
                'url' => $imagePath,
                'alt_text' => $alt ?: $product->name_fa,
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }
    }

    public function downloadImageToStorage(string $imageUrl): ?string
    {
        try {
            if (str_starts_with($imageUrl, '//')) {
                $imageUrl = 'https:' . $imageUrl;
            }

            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                ])
                ->get($imageUrl);

            if ($response->successful() && strlen($response->body()) > 500) {
                $parsedPath = (string) parse_url($imageUrl, PHP_URL_PATH);
                $extension = strtolower(pathinfo($parsedPath, PATHINFO_EXTENSION));
                if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'])) {
                    $extension = 'jpg';
                }

                $filename = 'products/' . Str::uuid() . '.' . $extension;
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $response->body());

                return $filename;
            }
        } catch (Throwable $e) {
            // Keep remote URL on failure
        }

        return null;
    }

    public function extractImages(string $source, string $sourceUrl): array
    {
        $found = [];

        // 1. Meta og:image and twitter:image
        if (preg_match_all('/<meta[^>]+(?:property|name)=["\'](?:og:image|twitter:image)["\'][^>]+content=["\']([^"\']+)["\']/i', $source, $metaMatches)) {
            $found = array_merge($found, $metaMatches[1]);
        }

        // 2. Img tags with src, data-src, data-zoom-image, data-original, data-imgs, data-lazy-src
        if (preg_match_all('/<img[^>]+(?:src|data-src|data-lazy-src|data-original|data-zoom-image|data-high-res-img|data-full)=["\']([^"\']+)["\']/i', $source, $imgMatches)) {
            $found = array_merge($found, $imgMatches[1]);
        }

        // 3. JSON-LD and script JSON image URLs
        if (preg_match_all('/"(?:image|imageUrl|imagePath|mainImage|picUrl|zoomImage)"\s*:\s*"([^"]+)"/i', $source, $jsonMatches)) {
            $found = array_merge($found, $jsonMatches[1]);
        }

        // 4. Raw image links inside source or text
        if (preg_match_all('/https?:\/\/[^\s"\'<>]+\.(?:jpg|jpeg|png|webp|avif)/i', $source, $rawUrlMatches)) {
            $found = array_merge($found, $rawUrlMatches[0]);
        }

        $cleanImages = [];
        foreach ($found as $url) {
            $url = trim(html_entity_decode(strip_tags($url)));
            if (empty($url)) {
                continue;
            }

            // Convert protocol-relative to https:
            if (str_starts_with($url, '//')) {
                $url = 'https:' . $url;
            } elseif (! filter_var($url, FILTER_VALIDATE_URL) && ! empty($sourceUrl)) {
                $parsedHost = parse_url($sourceUrl, PHP_URL_SCHEME) . '://' . parse_url($sourceUrl, PHP_URL_HOST);
                $url = rtrim($parsedHost, '/') . '/' . ltrim($url, '/');
            }

            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            // Filter out tracking icons, emojis, logos, avatars, gif sprites
            $lower = strtolower($url);
            if (preg_match('/(1x1|blank\.gif|loading\.gif|avatar|badge|favicon|sprite|logo_|\/icon\/|\.svg)/i', $lower)) {
                continue;
            }

            // Normalize CDN thumbnails to high-res (e.g. Alibaba / AliExpress / Taobao thumbnails)
            $url = preg_replace('/_(?:350x350|220x220|100x100|50x50|q90|sum)\.(?:jpg|png|webp)$/i', '', $url);
            $url = preg_replace('/\.jpg_\.webp$/i', '.jpg', $url);

            $cleanImages[] = $url;
        }

        return array_values(array_unique($cleanImages));
    }

    private function fetchSource(string $sourceUrl): string
    {
        if (! config('services.ai_product.fetch_external')) {
            return '';
        }

        try {
            $response = Http::timeout((int) config('services.ai_product.timeout', 20))
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.9,fa;q=0.8',
                ])
                ->get($sourceUrl);

            if (! $response->successful()) {
                // Do not throw; we can still parse product from URL slug
                return '';
            }

            return (string) $this->sanitizeUtf8($response->body());
        } catch (Throwable $e) {
            return '';
        }
    }

    private function extractProductData(string $sourceUrl, string $source, ?string $rawText = null): array
    {
        $slugName = $this->extractSlugName($sourceUrl);
        $fallback = [
            'source_url' => $sourceUrl,
            'fetched_at' => now()->toIso8601String(),
            'provider' => 'local-parser',
            'name' => $slugName,
            'name_en' => $slugName,
            'seo_title' => $slugName,
            'seo_slug' => Str::slug($slugName),
            'images' => [],
        ];

        $pageTitle = $source !== '' ? $this->extractTitle($source) : null;
        $images = $source !== '' ? $this->extractImages($source, $sourceUrl) : [];

        if (filled($rawText)) {
            // Also extract images from rawText if user pasted HTML/URLs
            $images = array_merge($images, $this->extractImages($rawText, $sourceUrl));
        }

        $fallback['page_title'] = $pageTitle;
        $fallback['images'] = array_values(array_unique($images));

        $apiKey = config('services.ai_product.api_key');
        if (blank($apiKey)) {
            $fallback['name'] = $fallback['page_title'] ?: $fallback['name'];
            return $fallback;
        }

        $baseUrl = rtrim((string) config('services.ai_product.base_url', 'https://api.openai.com/v1'), '/');
        $endpoint = "{$baseUrl}/chat/completions";

        $cleanedText = $this->cleanHtmlContent($source);

        // Check if page was protected by CAPTCHA / bot check / empty JS shell
        $isCaptchaOrEmpty = strlen($cleanedText) < 120 
            || stripos($source, 'punycode') !== false 
            || stripos($source, 'security check') !== false 
            || stripos($source, 'verify you are human') !== false;

        $promptContent = "Source URL: {$sourceUrl}\n";
        $promptContent .= "Extracted Product Title / Model from URL: {$slugName}\n";

        if (filled($rawText)) {
            $promptContent .= "Provided Product Specs / Description:\n" . Str::limit(trim($rawText), 6000) . "\n";
        } elseif (! $isCaptchaOrEmpty) {
            $promptContent .= "Page Content:\n" . Str::limit($cleanedText, 4000) . "\n";
        } else {
            $promptContent .= "Note: The remote web page returned a security check/empty shell. Please generate complete, professional, accurate B2B wholesale product specifications and Persian SEO descriptions based on the product name and model: '{$slugName}'.\n";
        }

        $response = Http::timeout((int) config('services.ai_product.timeout', 30))
            ->withToken($apiKey)
            ->post($endpoint, [
                'model' => config('services.ai_product.model', 'qwen/qwen3.8-27b'),
                'temperature' => 0.1,
                'max_tokens' => 850,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an e-commerce specialist for B2B wholesale. Extract product details and generate concise, professional Persian copy suitable for justified paragraphs. Output ONLY a valid JSON object with keys: name (Persian title), name_en (English title), brand_name, sku, model_number, country_of_origin, short_desc (Persian summary), long_desc (Persian detailed description), specifications (JSON object of key-value pairs), seo_title (Persian SEO title), seo_slug (english slug), seo_desc (Persian SEO description), images (array of image URLs if found). Do not include any markdown or conversational text. Output pure JSON only.'],
                    ['role' => 'user', 'content' => $promptContent],
                ],
            ]);

        if (! $response->successful()) {
            $errorMessage = $response->json('error.message') ?? $response->body();
            // If it's a rate limit / token limit error from provider, fallback to slug-based data gracefully
            if (stripos($errorMessage, 'rate limit') !== false || stripos($errorMessage, 'limit') !== false) {
                $fallback['name'] = $fallback['page_title'] ?: $fallback['name'];
                $fallback['seo_title'] = $fallback['page_title'] ?: $fallback['name'];
                return $fallback;
            }
            throw new RuntimeException("ارتباط با سرویس هوش مصنوعی برقرار نشد: {$errorMessage}");
        }

        $content = (string) $response->json('choices.0.message.content');
        $decoded = $this->parseJsonPayload($content);

        if (! is_array($decoded)) {
            $fallback['name'] = $fallback['page_title'] ?: $fallback['name'];
            $fallback['seo_title'] = $fallback['page_title'] ?: $fallback['name'];
            $fallback['raw_response'] = $content;
            return $fallback;
        }

        // Preserve images found
        if (empty($decoded['images']) && !empty($fallback['images'])) {
            $decoded['images'] = $fallback['images'];
        }

        return array_merge($fallback, $decoded);
    }

    public function extractSlugName(string $url): string
    {
        $path = (string) parse_url($url, PHP_URL_PATH);
        $basename = basename($path);
        $basename = preg_replace('/\.(html|htm|php|jsp|aspx)$/i', '', $basename);
        $basename = preg_replace('/[_\-]\d{8,}/', '', $basename);
        $basename = preg_replace('/^(product-detail|item|product|dp|p)\/?/i', '', $basename);
        $clean = trim(preg_replace('/[_\-]+/', ' ', $basename));

        return $clean ?: Str::headline((string) parse_url($url, PHP_URL_HOST));
    }

    private function parseJsonPayload(string $content): ?array
    {
        $content = trim($content);
        if ($content === '') {
            return null;
        }

        // 1. Direct JSON decode
        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // 2. Extract from markdown code blocks: ```json ... ``` or ``` ... ```
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $content, $matches)) {
            $extracted = trim($matches[1]);
            $decoded = json_decode($extracted, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // 3. Find outermost curly braces { ... }
        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $jsonSubstring = substr($content, $start, $end - $start + 1);
            $decoded = json_decode($jsonSubstring, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            // Clean common minor syntax issues (e.g. trailing commas before closing braces/brackets)
            $cleanedJson = preg_replace('/,\s*([\}\]])/', '$1', $jsonSubstring);
            $decoded = json_decode($cleanedJson, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // 4. Line by line regex key extractor if JSON structure has syntax flaws
        $keys = ['name', 'name_en', 'brand_name', 'sku', 'model_number', 'country_of_origin', 'short_desc', 'long_desc', 'seo_title', 'seo_slug', 'seo_desc'];
        $extractedObj = [];
        foreach ($keys as $key) {
            if (preg_match('/"' . preg_quote($key, '/') . '"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/s', $content, $m)) {
                $val = json_decode('"' . $m[1] . '"');
                $extractedObj[$key] = is_string($val) ? $val : stripcslashes($m[1]);
                $extractedObj[$key] = (string) $this->sanitizeUtf8($extractedObj[$key]);
            }
        }

        if (!empty($extractedObj['name']) || !empty($extractedObj['name_en'])) {
            return $extractedObj;
        }

        return null;
    }

    private function extractTitle(string $source): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $source, $matches) !== 1) {
            return null;
        }

        return trim(html_entity_decode(strip_tags($matches[1])));
    }

    private function cleanHtmlContent(string $html): string
    {
        // Remove script, style, SVG, noscript tags and their contents
        $html = preg_replace('/<(script|style|svg|noscript)[^>]*>.*?<\/\1>/is', ' ', $html);
        
        // Strip other HTML tags
        $text = strip_tags($html);

        // Normalize multiple whitespaces and newlines
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n\s*\n+/', "\n", $text);

        return trim($text);
    }
}
