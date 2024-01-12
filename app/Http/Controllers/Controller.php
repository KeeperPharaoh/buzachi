<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var array|string
     */
    private $locale;

    public function __construct ()
    {
        $this->locale = request()->header('Accept-Language') ?? 'ru';
    }

    public function getLocale ()
    {
        return $this->locale;
    }

    public function setLocale (string $locale)
    {
        $this->locale = $locale;
    }
}
