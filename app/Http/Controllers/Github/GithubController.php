<?php

namespace App\Http\Controllers\Github;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GithubControllerController extends Controller
{
    public function commits(Request $request)
    {
        isset($request->branch) ? $request->branch : $request->branch = 'master';
        $path = Str::of(parse_url($request->url)['path'])->ltrim('/');
        $info = [
            'owner' => explode('/', $path)[0],
            'repo' => explode('/', $path)[1]
        ];

        $response = Http::get('https://api.github.com/repos/'.$info['owner'].'/'.$info['repo'].'/commits', [
            'per_page' => 5,
            'sha' => $request->branch
        ])->json();

        return $this->formatCommit($response);
    }

    public function branches(Request $request)
    {
        $path = Str::of(parse_url($request->url)['path'])->ltrim('/');
        $info = [
            'owner' => explode('/', $path)[0],
            'repo' => explode('/', $path)[1]
        ];
        $response = Http::get('https://api.github.com/repos/'.$info['owner'].'/'.$info['repo'])->json();

        return $response;
    }

    private function formatCommit($commits)
    {
        if (isset($commits['message'])) {
            return response($commits['message'], 404); // Commits Not foud
        } else {
            return response(collect($commits)->map(function ($commit) {
                return [
                            'name' => $commit['commit']['author']['name'],
                            'email' => $commit['commit']['author']['email'],
                            'date' => Carbon::parse($commit['commit']['author']['date'])->diffForHumans(),
                            'url' => $commit['html_url'],
                            'message' => $commit['commit']['message'],
                        ];
            })->toArray(), 200);
        }
    }
}
