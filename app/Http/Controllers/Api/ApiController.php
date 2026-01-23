<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;

abstract class ApiController extends Controller
{
    use ApiResponse;
}
