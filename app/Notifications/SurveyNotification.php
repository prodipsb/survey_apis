<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SurveyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $surveyId; 
    protected $username; 
    public function __construct($survey)
    {
        $this->surveyId=$survey->id;
        $this->username=$survey->binHolderName;

        // $this->surveyId=$survey["survey_id"];
        // $this->username=$survey["user_name"];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }


    // public function toDataBase($notifiable)
    // {
    //     return (new Database)
    //     ->content('A user submitted a form  to your application . $this->user->first(1->name)');

       
    // }



    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'survey_id' => $this->surveyId,
            'submitted_user' => $this->username,
            'message' => 'New form submitted by ' . $this->username,
        ];
    }
}
