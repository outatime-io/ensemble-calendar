<?php

namespace App\Http\Controllers;

use App\Models\Rehearsal;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RehearsalPlanController extends Controller
{
    public function show(Rehearsal $rehearsal): StreamedResponse
    {
        if (! $rehearsal->plan_path || ! Storage::disk('private')->exists($rehearsal->plan_path)) {
            abort(404);
        }

        $filename = Str::slug($rehearsal->title).'-probeplan.pdf';

        return Storage::disk('private')->download($rehearsal->plan_path, $filename);
    }
}
