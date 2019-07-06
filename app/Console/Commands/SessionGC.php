<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SessionGc extends Command
{
    protected $signature = 'session:gc';

    public function handle()
    {
        $session = $this->getLaravel()->make('session');
        $lifetime = Arr::get($session->getSessionConfig(), 'lifetime') * 60;
        $session->getHandler()->gc($lifetime);
    }
}