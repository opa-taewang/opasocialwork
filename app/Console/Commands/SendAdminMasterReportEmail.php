<?php

namespace App\Console\Commands;

class SendAdminMasterReportEmail extends \Illuminate\Console\Command
{
    protected $signature = "send-status-report";
    protected $description = "Send Master report to admin";
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        \Illuminate\Support\Facades\Mail::to(getOption("notify_email"))->send(new \App\Mail\AdminMasterReport());
    }
}

?>