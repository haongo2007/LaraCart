<?php
use App\Mail\SendMail;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Mail;

/**
 * Function send mail
 * Mail queue to run need setting crontab for php artisan schedule:run
 *
 * @param   [string]  $view            Path to view
 * @param   array     $dataView        Content send to view
 * @param   array     $emailConfig     to, cc, bbc, subject..
 * @param   array     $attach      Attach file
 *
 * @return  mixed
 */
function lc_send_mail($dataView, array $emailConfig = [], array $attach = [])
{
    if (!empty(lc_config('email_action_mode'))) {
        if (!empty(lc_config('email_action_queue'))) {
            dispatch(new SendEmailJob($dataView,  $emailConfig, $attach));
        } else {
            lc_process_send_mail($dataView,  $emailConfig, $attach);
        }
    } else {
        return false;
    }
}

/**
 * Process send mail
 *
 * @param   [type]  $view         [$view description]
 * @param   array   $dataView     [$dataView description]
 * @param   array   $emailConfig  [$emailConfig description]
 * @param   array   $attach       [$attach description]
 *
 * @return  [][][]                [return description]
 */
function lc_process_send_mail($dataView, array $emailConfig = [], array $attach = []) {
    try {
        $status = Mail::send(new SendMail($dataView, $emailConfig, $attach));
        if ($status) {
            dd($status);
        }
    } catch (\Throwable $e) {
        lc_report("Sendmail subject: ". $emailConfig['subject'] . PHP_EOL . $e->getMessage());
    }
}
