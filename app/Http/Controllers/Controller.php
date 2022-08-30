<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;

    protected $http;

    public function __construct()
    {
        $this->http = Http::remote('remote')->asForm();
    }
}
