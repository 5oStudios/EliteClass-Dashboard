<?php

namespace App\Services;

use Aws\Ses\SesClient;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class SESService
{
    protected $sesClient;

    public function __construct()
    {
        $this->sesClient = new SesClient([
            'version' => 'latest',
            'region' => config('services.ses.region'),
            'credentials' => [
                'key' => config('services.ses.key'),
                'secret' => config('services.ses.secret'),
            ],
        ]);
    }

    public function sendEmail($to, $subject, $bladeView,$data=[])
    {
        $htmlContent = View::make($bladeView, $data)->render();

        $result = $this->sesClient->sendEmail([
            'Destination' => [
                'ToAddresses' => [$to],
            ],
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => 'UTF-8',
                        'Data' => $htmlContent,
                    ],
                ],

                'Subject' => [
                    'Charset' => 'UTF-8',
                    'Data' => $subject,
                ],
            ],
            'Source' => 'no-reply@elite-class.com',
        ]);

        return $result;
    }
}