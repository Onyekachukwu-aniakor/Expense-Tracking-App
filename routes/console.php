<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(command:'expenses:generate-recurring-expense')
//->dailyAt(time:'00:00')
->everyFifteenSeconds()
->withoutOverlapping()
->onSuccess(callback:function(){
    Log::info(message:'Recurring expense generated successfully');
})
->onFailure(callback:function(){
    Log::info(message:'Failed to generate recurring expenses');
});
