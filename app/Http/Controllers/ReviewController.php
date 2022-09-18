<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    //

    public function next() {
        $hosts = Host::where('status', 'running')->whereNotNull('custom_domain')->whereIn('protocol', ['http', 'https'])->whereNull('review_at')->simplePaginate(1);


        // screenshot url
        $hosts->map(function ($host) {
            $today = date('Y-m-d');
            // get from public storage

            $path = 'reviews/' . $today . '/screenshots/' . $host->id . '.png';

            // if exists
            if (Storage::disk('public')->exists($path)) {
                $host->screenshot_url = Storage::disk('public')->url($path);
            } else {
                $host->screenshot_url = null;
            }

        });

        return view('reviews.next', compact('hosts'));
    }
}
