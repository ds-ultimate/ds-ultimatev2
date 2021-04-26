<?php

namespace App\Console\Commands;

use App\Notifications\DiscordNotificationQueueElement;
use Illuminate\Console\Command;

class SendDiscordNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:discordNotifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hintergrund Job fürs senden von Notificaions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = new DiscordNotificationQueueElement();
        while(($data = $model->first()) !== null) {
            $data->send();
            sleep(1);
        }
        
        return 0;
    }
}
