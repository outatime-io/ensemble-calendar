<?php

namespace App\Http\Controllers;

use App\Models\Rehearsal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RehearsalPlanController extends Controller
{
    public function show(Request $request, Rehearsal $rehearsal): StreamedResponse
    {
        if (! $rehearsal->plan_path || ! Storage::disk('private')->exists($rehearsal->plan_path)) {
            abort(404);
        }

        if (! $request->user()) {
            $feedToken = config('calendar.feed_token');
            $token = (string) $request->query('token', '');

            abort_unless(is_string($feedToken) && hash_equals($feedToken, $token), 404);
        }

        $filename = Str::slug($rehearsal->title).'-probeplan.pdf';

        return Storage::disk('private')->download($rehearsal->plan_path, $filename);
    }
}
