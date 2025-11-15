<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdvisorSettingsController extends Controller
{
    /**
     * Display the advisor settings page.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('AdvisorSettings', [
            'advisor' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
        ]);
    }
}


