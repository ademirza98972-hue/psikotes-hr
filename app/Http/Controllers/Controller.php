<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /**
     * Ambil nilai per_page dari request dengan validasi dan default.
     *
     * @param int[]|null $options
     */
    protected function getPerPage(Request $request, int $default = 15, ?array $options = null): int
    {
        $valid = $options ?? self::PER_PAGE_OPTIONS;
        return in_array((int) $request->input('per_page'), $valid, true)
            ? (int) $request->input('per_page')
            : $default;
    }
}
