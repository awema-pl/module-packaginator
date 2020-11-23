<?php

namespace AwemaPL\Packaginator\Sections\Creators\Services;
use Illuminate\Support\Str;

class PackageNameService
{

    /**
     * Build name
     *
     * @param $blankNamePackage
     * @return string
     */
    public function buildName($blankNamePackage)
    {
        return Str::ucfirst(mb_strtolower(preg_replace("/[^a-zA-Z]+/", "", $blankNamePackage)));
    }
}
