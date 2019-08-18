<?php

namespace App\Http\Controllers;

use App\Changelog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
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
                    return $this->release($payload);
                default:
                    return $request->header('X-GitHub-Event').' is not supported';
            }
        }else{
            echo 'Hash is not correct';
        }
    }

    private function release($payload){
        switch ($payload->action){
            case 'published':
                return $this->releasePublished($payload);
            case 'deleted':
                return 'deleted is not supported';
            default:
                return $payload->action.' is not supported';
        }
    }

    private function releasePublished($payload){
        $changelog = new Changelog();

        $changelog->version = $payload->release->tag_name;
        $changelog->title = $payload->release->name;
        $changelog->content = $payload->release->body;
        $changelog->repository_html_url = $payload->repository->html_url;

        if($changelog->save()){


            if (env('APP_DEBUG') == false) {
                $root_path = base_path();
                $process = new Process('cd ' . $root_path . '; ./deploy.sh');
                $process->run(function ($type, $buffer) {
                    echo $buffer;
                });
            }
            echo 'sucsess';
        }else{
            Log::debug('Release failed');
            echo 'failed';
        }
    }

}

