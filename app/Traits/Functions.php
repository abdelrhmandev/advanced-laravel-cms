<?php
namespace App\Traits;
use App\Models\Menus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
/**
 * Trait UploadAble
 * @package App\Traits
 */
trait Functions
{
    // convert 'theme settings' hex values to rgb
private function hexToRgba($hex, $opacity = 1)
{
    // Remove the # if it exists
    $hex = str_replace("#", "", $hex);

    // Convert Hex to RGB
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }

    return [
        'r' => $r,
        'g' => $g,
        'b' => $b,
        'a' => (float)$opacity
    ];
}


    function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    function make_slug($string = null, $separator = '-')
    {
        if (is_null($string)) {
            return '';
        }

        // Trim spaces and convert to lowercase
        $string = trim($string);
        $string = mb_strtolower($string, 'UTF-8');

        // Keep Arabic and Latin alphanumeric characters only
        $string = preg_replace('/[^a-z0-9_\sءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى-]/u', '', $string);

        // Replace multiple spaces or dashes with a single space
        $string = preg_replace('/[\s-]+/', ' ', $string);

        // Replace spaces and underscores with the given separator
        $string = preg_replace('/[\s_]/', $separator, $string);

        return $string;
    }
}
