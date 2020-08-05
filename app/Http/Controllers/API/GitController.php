<?php

namespace App\Http\Controllers\API;

use App\Changelog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class GitController extends Controller
{

    private $buffer;

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
        $contents = $this->splitContent($payload->release->body);
        foreach ($contents as $key => $content){
            if(in_array($key, config('dsUltimate.changelog_lang_key'))){
                $changelog->$key = $content;
            }
        }
        $changelog->repository_html_url = $payload->repository->html_url;

        if($changelog->save()){


            if (config('app.debug') == false) {
                $root_path = base_path();
                $process = Process::fromShellCommandline('cd ' . $root_path . '; ./deploy.sh');
                $process->run(function ($type, $buffer) {
                    $this->buffer .= $buffer;
                    echo $buffer;
                });
            }
            $changelog->buffer = nl2br($this->buffer);
            $changelog->save();
            echo 'sucsess';
        }else{
            Log::debug('Release failed');
            echo 'failed';
        }
    }

    private function splitContent($body){
        $keys = config('dsUltimate.changelog_lang_key');
        $i = 0;
        $split = '/(';
        foreach ($keys as $key){
            $split .= '\['.$key.'\]';
            if ($i != count($keys) - 1){
                $split .= '|';
            }
            $i++;
        }

        $split .= ')/';

        $contentArrays = preg_split($split, $body, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $lang_key = '';
        foreach ($contentArrays as $key => $contentArray){
            if ($key % 2 === 0){
                $lang_key = str_replace(array('[', ']'), '', $contentArray);
            }else{
                $content[$lang_key] = nl2br(trim($contentArray));
            }
        }

        return $content;
    }

}

