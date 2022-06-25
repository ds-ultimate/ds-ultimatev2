<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CaptchaGC extends Command
{
    protected $signature = 'captcha:gc';

    public function handle()
    {
        \Captcha\Captcha::gc(15*60);
        return 0;
    }
}