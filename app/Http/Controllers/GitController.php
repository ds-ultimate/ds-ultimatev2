<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Changelog;
use App\News;
use App\Player;
use App\Server;
use App\Util\BasicFunctions;
use App\World;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;


class GitController extends BaseController
{
    public function index(Request $request)
    {

        $githubPayload = $request->getContent();
        $githubHash = $request->header('X-Hub-Signature');
        $payload = json_decode($githubPayload);

        $localToken = config('app.deploy_secret');
        $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $localToken, false);

        if (hash_equals($githubHash, $localHash)) {

            switch ($request->header('X-GitHub-Event')){
                case 'release':
                    $this->release($payload);
                    break;
                default:
                    echo $request->header('X-GitHub-Event').' is not supported';
                    break;
            }
        }
    }

    private function release($payload){
        switch ($payload->action){
            case 'published':
                $this->releasePublished($payload);
                break;
            case 'deleted':
                echo 'deleted is not supported';
                break;
            default:
                echo $payload->action.' is not supported';
                break;
        }
    }

    private function releasePublished($payload){
        $changelog = new Changelog();

        $changelog->version = $payload->release->tag_name;
        $changelog->title = $payload->release->name;
        $changelog->content = $payload->release->body;
        $changelog->repository_html_url = $payload->repository->html_url;

        if($changelog->save()){
            echo 'sucsess';

            if (env('APP_DEBUG') == false) {
                $root_path = base_path();
                $process = new Process('cd ' . $root_path . '; ./deploy.sh');
                $process->run(function ($type, $buffer) {
                    echo $buffer;
                });
            }

        }else{
            echo 'failed';
            Log::debug('Release failed');
        }
    }

}
