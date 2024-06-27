<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\SurveyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class SendSurveyFormSubmissionNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        $notifiableUsers = User::whereHas('roles', function ($query) {
            $query->where('user_type', 'admin');
        })->get();

        if(Auth::user()->supervisor_id){
            $supervisors = User::where('role_id', Auth::user()->supervisor_id)->get();
            $notifiableUsers = $notifiableUsers->merge($supervisors);
        }


        if(Auth::user()->reporting_role_id){
            $reportedUsers = User::where('role_id', Auth::user()->reporting_role_id)->get();
            $notifiableUsers = $notifiableUsers->merge($reportedUsers);
        }

        Notification::send($notifiableUsers, new SurveyNotification($event->survey));
    }


}
