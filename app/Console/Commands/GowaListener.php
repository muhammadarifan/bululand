<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Ratchet\Client\Connector;
use React\EventLoop\Loop;

#[Signature('app:gowa-listener')]
#[Description('Command description')]
class GowaListener extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $loop = Loop::get();

        $connector = new Connector($loop);

        $connector(
            'wss://gowa.arifslab.web.id/ws?device_id=4736f5e7-ca3f-4d57-932b-ec025985edce',
            [],
            [
                'Authorization' => 'Basic '.base64_encode(
                    'admin:admin'
                ),
            ]
        )
            ->then(function ($conn) {

                $this->info('Connected: '.json_encode($conn));

                $conn->on('message', function ($msg) {

                    $payload = json_decode($msg, true);

                    logger()->info('WS Message', $payload);

                    $this->info('WS Message: '.$msg);

                    // dispatch job
                    // ProcessIncomingMessageJob::dispatch($payload);
                });

                $conn->on('close', function ($code = null, $reason = null) {
                    logger()->warning('Connection closed');
                });
            });

        $loop->run();
    }
}
