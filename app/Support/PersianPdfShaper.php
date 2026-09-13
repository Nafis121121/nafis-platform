<?php

namespace App\Support;

class PersianPdfShaper
{
    /**
     * Map of Persian / Arabic characters to their presentation forms:
     * [Isolated, Final, Initial, Medial]
     * For non-connecting characters, Initial and Medial are null.
     */
    protected static array $glyphs = [
        // Alef with Madda
        "\u{0622}" => ["\u{FE81}", "\u{FE82}", null, null],
        // Alef with Hamza Above
        "\u{0623}" => ["\u{FE83}", "\u{FE84}", null, null],
        // Waw with Hamza Above
        "\u{0624}" => ["\u{FE85}", "\u{FE86}", null, null],
        // Alef with Hamza Below
        "\u{0625}" => ["\u{FE87}", "\u{FE88}", null, null],
        // Yeh with Hamza Above (ئ)
        "\u{0626}" => ["\u{FE89}", "\u{FE8A}", "\u{FE8B}", "\u{FE8C}"],
        // Alef (ا)
        "\u{0627}" => ["\u{FE8D}", "\u{FE8E}", null, null],
        // Beh (ب)
        "\u{0628}" => ["\u{FE8F}", "\u{FE90}", "\u{FE91}", "\u{FE92}"],
        // Teh Marbuta (ة)
        "\u{0629}" => ["\u{FE93}", "\u{FE94}", null, null],
        // Teh (ت)
        "\u{062A}" => ["\u{FE95}", "\u{FE96}", "\u{FE97}", "\u{FE98}"],
        // Theh (ث)
        "\u{062B}" => ["\u{FE99}", "\u{FE9A}", "\u{FE9B}", "\u{FE9C}"],
        // Jeem (ج)
        "\u{062C}" => ["\u{FE9D}", "\u{FE9E}", "\u{FE9F}", "\u{FEA0}"],
        // Hah (ح)
        "\u{062D}" => ["\u{FEA1}", "\u{FEA2}", "\u{FEA3}", "\u{FEA4}"],
        // Khah (خ)
        "\u{062E}" => ["\u{FEA5}", "\u{FEA6}", "\u{FEA7}", "\u{FEA8}"],
        // Dal (د)
        "\u{062F}" => ["\u{FEA9}", "\u{FEAA}", null, null],
        // Thal (ذ)
        "\u{0630}" => ["\u{FEAB}", "\u{FEAC}", null, null],
        // Reh (ر)
        "\u{0631}" => ["\u{FEAD}", "\u{FEAE}", null, null],
        // Zain (ز)
        "\u{0632}" => ["\u{FEAF}", "\u{FEB0}", null, null],
        // Seen (س)
        "\u{0633}" => ["\u{FEB1}", "\u{FEB2}", "\u{FEB3}", "\u{FEB4}"],
        // Sheen (ش)
        "\u{0634}" => ["\u{FEB5}", "\u{FEB6}", "\u{FEB7}", "\u{FEB8}"],
        // Sad (ص)
        "\u{0635}" => ["\u{FEB9}", "\u{FEBA}", "\u{FEBB}", "\u{FEBC}"],
        // Dad (ض)
        "\u{0636}" => ["\u{FEBD}", "\u{FEBE}", "\u{FEBF}", "\u{FEC0}"],
        // Tah (ط)
        "\u{0637}" => ["\u{FEC1}", "\u{FEC2}", "\u{FEC3}", "\u{FEC4}"],
        // Zah (ظ)
        "\u{0638}" => ["\u{FEC5}", "\u{FEC6}", "\u{FEC7}", "\u{FEC8}"],
        // Ain (ع)
        "\u{0639}" => ["\u{FEC9}", "\u{FECA}", "\u{FECB}", "\u{FECC}"],
        // Ghain (غ)
        "\u{063A}" => ["\u{FECD}", "\u{FECE}", "\u{FECF}", "\u{FED0}"],
        // Feh (ف)
        "\u{0641}" => ["\u{FED1}", "\u{FED2}", "\u{FED3}", "\u{FED4}"],
        // Qaf (ق)
        "\u{0642}" => ["\u{FED5}", "\u{FED6}", "\u{FED7}", "\u{FED8}"],
        // Arabic Kaf (ك)
        "\u{0643}" => ["\u{FED9}", "\u{FEDA}", "\u{FEDB}", "\u{FEDC}"],
        // Persian Keheh (ک)
        "\u{06A9}" => ["\u{FB8E}", "\u{FB8F}", "\u{FB90}", "\u{FB91}"],
        // Gaf (گ)
        "\u{06AF}" => ["\u{FB92}", "\u{FB93}", "\u{FB94}", "\u{FB95}"],
        // Lam (ل)
        "\u{0644}" => ["\u{FEDD}", "\u{FEDE}", "\u{FEDF}", "\u{FEE0}"],
        // Meem (م)
        "\u{0645}" => ["\u{FEE1}", "\u{FEE2}", "\u{FEE3}", "\u{FEE4}"],
        // Noon (ن)
        "\u{0646}" => ["\u{FEE5}", "\u{FEE6}", "\u{FEE7}", "\u{FEE8}"],
        // Heh (ه)
        "\u{0647}" => ["\u{FEE9}", "\u{FEEA}", "\u{FEEB}", "\u{FEEC}"],
        // Heh Gol / Urdu (ہ / ھ)
        "\u{06C1}" => ["\u{FEE9}", "\u{FEEA}", "\u{FEEB}", "\u{FEEC}"],
        // Waw (و)
        "\u{0648}" => ["\u{FEED}", "\u{FEEE}", null, null],
        // Arabic Yeh (ي)
        "\u{064A}" => ["\u{FEF1}", "\u{FEF2}", "\u{FEF3}", "\u{FEF4}"],
        // Alef Maksura (ى)
        "\u{0649}" => ["\u{FEEF}", "\u{FEF0}", "\u{FEF3}", "\u{FEF4}"],
        // Farsi Yeh (ی)
        "\u{06CC}" => ["\u{FBFC}", "\u{FBFD}", "\u{FBFE}", "\u{FBFF}"],
        // Pe (پ)
        "\u{067E}" => ["\u{FB56}", "\u{FB57}", "\u{FB58}", "\u{FB59}"],
        // Che (چ)
        "\u{0686}" => ["\u{FB7A}", "\u{FB7B}", "\u{FB7C}", "\u{FB7D}"],
        // Zhe (ژ)
        "\u{0698}" => ["\u{FB8A}", "\u{FB8B}", null, null],
        // Hamza (ء)
        "\u{0621}" => ["\u{FE80}", null, null, null],
        // Heh with Yeh above (ۀ)
        "\u{06C0}" => ["\u{FBA4}", "\u{FBA5}", null, null],
    ];

    /**
     * Lam-Alef ligature mapping: [Isolated, Final]
     */
    protected static array $lamAlef = [
        "\u{0622}" => ["\u{FEF5}", "\u{FEF6}"], // lآ
        "\u{0623}" => ["\u{FEF7}", "\u{FEF8}"], // lأ
        "\u{0625}" => ["\u{FEF9}", "\u{FEFA}"], // lإ
        "\u{0627}" => ["\u{FEFB}", "\u{FEFC}"], // lا
    ];

    /**
     * Shape entire HTML document so DomPDF renders Persian without reversed/broken characters.
     */
    public static function shapeHtml(string $html): string
    {
        return preg_replace_callback('/(>)([^<]+)(<)/u', function ($matches) {
            $prefix = $matches[1];
            $text = $matches[2];
            $suffix = $matches[3];

            if (preg_match('/[\x{0600}-\x{06FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $text)) {
                $shaped = self::shape($text);
                return $prefix . $shaped . $suffix;
            }

            return $matches[0];
        }, $html);
    }

    /**
     * Shape a plain text string (combines glyph joining + BiDi reversal for PDF).
     */
    public static function shape(string $text): string
    {
        if (trim($text) === '') {
            return $text;
        }

        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $text));
        $shapedLines = [];

        foreach ($lines as $line) {
            $shapedLines[] = self::shapeLine($line);
        }

        return implode("\n", $shapedLines);
    }

    /**
     * Shape and order a single line of text.
     */
    protected static function shapeLine(string $line): string
    {
        if (trim($line) === '') {
            return $line;
        }

        $chars = mb_str_split($line);
        $count = count($chars);

        if ($count === 0) {
            return $line;
        }

        // Step 1: Contextual shaping of Arabic/Persian characters
        $shapedChars = [];
        $i = 0;
        while ($i < $count) {
            $char = $chars[$i];

            // Check Lam-Alef ligature
            if ($char === "\u{0644}" && isset($chars[$i + 1]) && isset(self::$lamAlef[$chars[$i + 1]])) {
                $nextChar = $chars[$i + 1];
                $prevChar = $i > 0 ? $chars[$i - 1] : null;
                $prevConnects = $prevChar !== null && self::characterConnectsRight($prevChar);

                $ligature = self::$lamAlef[$nextChar][$prevConnects ? 1 : 0];
                $shapedChars[] = $ligature;
                $i += 2;
                continue;
            }

            // Zero-width non-joiner
            if ($char === "\u{200C}") {
                $i++;
                continue;
            }

            if (isset(self::$glyphs[$char])) {
                $prevChar = $i > 0 ? $chars[$i - 1] : null;
                $nextChar = $i + 1 < $count ? $chars[$i + 1] : null;

                $prevConnects = $prevChar !== null && self::characterConnectsRight($prevChar);
                $nextConnects = $nextChar !== null && self::characterConnectsLeft($nextChar);

                $glyphForms = self::$glyphs[$char];

                if ($prevConnects && $nextConnects && isset($glyphForms[3]) && $glyphForms[3] !== null) {
                    $shapedChars[] = $glyphForms[3];
                } elseif ($nextConnects && isset($glyphForms[2]) && $glyphForms[2] !== null) {
                    $shapedChars[] = $glyphForms[2];
                } elseif ($prevConnects && isset($glyphForms[1]) && $glyphForms[1] !== null) {
                    $shapedChars[] = $glyphForms[1];
                } else {
                    $shapedChars[] = $glyphForms[0];
                }
            } else {
                $shapedChars[] = $char;
            }

            $i++;
        }

        // Step 2: BiDi visual reordering
        $tokens = [];
        $currentType = null;
        $currentBuffer = '';

        foreach ($shapedChars as $char) {
            $type = self::getCharDirectionType($char);

            if ($currentType === null) {
                $currentType = $type;
                $currentBuffer = $char;
            } elseif ($currentType === $type || $type === 'NEUTRAL') {
                $currentBuffer .= $char;
            } else {
                $tokens[] = ['type' => $currentType, 'text' => $currentBuffer];
                $currentType = $type;
                $currentBuffer = $char;
            }
        }

        if ($currentBuffer !== '') {
            $tokens[] = ['type' => $currentType, 'text' => $currentBuffer];
        }

        $reorderedTokens = array_reverse($tokens);
        $result = '';

        foreach ($reorderedTokens as $token) {
            if ($token['type'] === 'RTL') {
                $tokenChars = mb_str_split($token['text']);
                $reversedChars = array_reverse($tokenChars);

                foreach ($reversedChars as &$c) {
                    $c = match ($c) {
                        '(' => ')',
                        ')' => '(',
                        '[' => ']',
                        ']' => '[',
                        '{' => '}',
                        '}' => '{',
                        '«' => '»',
                        '»' => '«',
                        '<' => '>',
                        '>' => '<',
                        default => $c,
                    };
                }
                $result .= implode('', $reversedChars);
            } else {
                $result .= $token['text'];
            }
        }

        return $result;
    }

    protected static function characterConnectsLeft(string $char): bool
    {
        return isset(self::$glyphs[$char]) && self::$glyphs[$char][2] !== null;
    }

    protected static function characterConnectsRight(string $char): bool
    {
        return isset(self::$glyphs[$char]) && self::$glyphs[$char][1] !== null;
    }

    protected static function getCharDirectionType(string $char): string
    {
        if (preg_match('/^[0-9\/\.,:%+-]$/u', $char)) {
            return 'LTR';
        }

        if (preg_match('/^[a-zA-Z]$/u', $char)) {
            return 'LTR';
        }

        if (preg_match('/[\x{0600}-\x{06FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $char)) {
            return 'RTL';
        }

        return 'NEUTRAL';
    }
}
