<?php

namespace App\Http\Controllers\Github;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GithubController extends Controller
{
    public function commits(Request $request)
    {
        $branches = $this->branches($request->owner, $request->repo);
        if (is_null($branches)) {
            return response('Not found', 404);
        }
        $commitsByBranches = collect([]);
        foreach ($branches as $key => $branche) {
            $commits = Http::get('https://api.github.com/repos/'.$request->owner.'/'.$request->repo.'/commits', [
                'per_page' => 5,
                'sha' => $branche['name']
            ])->json();
            $commitsByBranches->put($branche['name'], $this->formatCommit($commits));
        }

        return $commitsByBranches;
    }

    private function branches($owner, $repo)
    {
        $defaultBranch = Http::get('https://api.github.com/repos/'.$owner.'/'.$repo)->json();
        if (isset($defaultBranch['default_branch'])) {
            $defaultBranch = $defaultBranch['default_branch'];
        } else {
            return null;
        }
        $response = Http::get('https://api.github.com/repos/'.$owner.'/'.$repo.'/branches')->json();
        return collect($response)->map(function ($branch) use ($defaultBranch) {
            return [
                'name' => $branch['name'],
                'default' => $branch['name'] === $defaultBranch,
            ];
        })->sortByDesc('default');
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
