<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SVP_Controller\Rekap\Concerns\HasAllowedSeeData;

abstract class RekapController extends Controller
{
    use HasAllowedSeeData;
}

