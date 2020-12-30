<?php

namespace App\Http\Controllers\Github;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GithubController extends Controller
{
    public function commits(Request $request)
    {
        $info = $this->parseUrl($request->url);
        $branches = $this->branches($request->url);
        $commitsByBranches = collect([]);
        foreach ($branches as $key => $branche) {
            $commits = Http::get('https://api.github.com/repos/'.$info['owner'].'/'.$info['repo'].'/commits', [
                'per_page' => 5,
                'sha' => $branche['name']
            ])->json();
            $commitsByBranches->put($branche['name'], $this->formatCommit($commits));
        }

        return $commitsByBranches;
    }

    private function branches($url)
    {
        $info = $this->parseUrl($url);
        $defaultBranch = Http::get('https://api.github.com/repos/'.$info['owner'].'/'.$info['repo'])->json()['default_branch'];
        $response = Http::get('https://api.github.com/repos/'.$info['owner'].'/'.$info['repo'].'/branches')->json();

        return collect($response)->map(function ($branch) use ($defaultBranch) {
            return [
                'name' => $branch['name'],
                'default' => $branch['name'] === $defaultBranch,
            ];
        })->sortByDesc('default');
    }

    private function parseUrl($url)
    {
        $path = Str::of(parse_url($url)['path'])->ltrim('/');
        return [
            'owner' => explode('/', $path)[0],
            'repo' => explode('/', $path)[1]
        ];
    }

    private function formatCommit($commits)
    {
        if (isset($commits['message'])) {
            return collect([]); // Commits Not foud
        } else {
            return collect($commits)->map(function ($commit) {
                return [
                            'name' => $commit['commit']['author']['name'],
                            'email' => $commit['commit']['author']['email'],
                            'date' => Carbon::parse($commit['commit']['author']['date'])->diffForHumans(),
                            'url' => $commit['html_url'],
                            'message' => $commit['commit']['message'],
                        ];
            });
        }
    }
}
