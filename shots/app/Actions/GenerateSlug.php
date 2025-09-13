<?php
namespace App\Actions;

use App\Models\Screenshot;
use Illuminate\Support\Str;

class GenerateSlug
{
    /**
     * Generate a unique base62 slug (7-10 chars)
     */
    public function handle(): string
    {
        do {
            $length = random_int(7, 10);
            $slug = $this->base62(random_bytes($length));
        } while (Screenshot::where('slug', $slug)->exists());
        return $slug;
    }

    /**
     * Convert bytes to base62 string
     */
    protected function base62(string $bytes): string
    {
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = gmp_import($bytes);
        $out = '';
        while (gmp_cmp($num, 0) > 0) {
            list($num, $rem) = [gmp_div_q($num, 62), gmp_intval(gmp_mod($num, 62))];
            $out = $alphabet[$rem] . $out;
        }
        return $out;
    }
}
