<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\PushNotificationResource;
use App\Http\Traits\GlobalTraits;
use App\Models\Device;
use App\Models\Notification;
use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{

    use GlobalTraits;

    protected $model = 'PushNotification';

    protected $limit = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            if(auth()->user()->user_type == "admin"){
                $pushNotifications = PushNotification::query();
               // return $this->throwMessage(200, 'success', 'All the list of $pushNotifications ', $pushNotifications);
            }else{
                $pushNotifications = PushNotification::where('receiver_id',auth()->user()->id);
            }


            $pushNotifications = $pushNotifications->orderBy('read_at', 'asc');
            $pushNotifications = $pushNotifications->orderBy('created_at', 'desc');

            $pushNotifications = $pushNotifications->paginate($this->limit);
            return PushNotificationResource::collection($pushNotifications);

            //  return $this->throwMessage(200, 'success', 'All the list of notifications ', $listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }


    public function unreadNotifications()
    {
        try {

            $unreadNotifications = auth()->user()->unreadnotifications();

            $unreadNotifications = $unreadNotifications->orderBy('read_at', 'asc');
            $unreadNotifications = $unreadNotifications->orderBy('created_at', 'desc');

            $unreadNotifications = $unreadNotifications->get();

            return $this->throwMessage(200, 'success', 'All the list of unread notifications ', $unreadNotifications);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        try {

            $notification = Notification::findOrFail($id);
            return $this->throwMessage(200, 'success', 'Notification data ', $notification);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }


    public function read(Request $request)
    {
        $id = $request->id;

        try {

            $notification = auth()->user()->notifications->where('id', $id)->markAsRead();
            return $this->throwMessage(200, 'success', 'Notification data ', $notification);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function allNotificationRead(Request $request)
    {

        try {

            auth()->user()->notifications->markAsRead();
            return $this->throwMessage(200, 'success', 'All Notification Mark As Read ');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function pushNotificationSend(Request $request)
    {

        $inputs = $request->all();

        $rules = [
            'notification' => 'required',
        ];
        

        $validation = Validator::make($inputs, $rules);

        if ($validation->fails()) {

            return $this->throwMessage(422, 'error', $validation->errors()->first());
        }


        $notification = new PushNotification();

        if($request->notificationType == "Role"){

            
            $roles = $request->roles;
            // $roleUsers = Device::whereIn('role_id', $roles)->with('role')->get();
            // return $this->throwMessage(200, 'success', 'Push Notification Stored Successfully ', $roleUsers);


            foreach($roles as $role){
                $roleUsers = User::where('role_id', $role['id'])->get();

                foreach($roleUsers as $roleUser){

                    $notification = new PushNotification();
                    $notification->type = $request->notificationType;
                    $notification->sender_id = $request->sender['id'];
                    $notification->sender_name = $request->sender['name'];
                    $notification->receiver_id = $roleUser['id'];
                    $notification->receiver_name = $roleUser['name'];
                    $notification->sender_role_id = $request->sender['role_id'];
                    $notification->sender_role_name = $request->sender['role'];
                    $notification->receiver_role_id = $role['id'];
                    $notification->receiver_role_name = $role['name'];
                    $notification->message_title = $request->notification['title'];
                    $notification->message = $request->notification['body'];
                    $notification->save();

                }
            }

        }else{
            $deviceTokens = $request->deviceTokens;
            foreach($deviceTokens as $deviceToken){

                $notification = new PushNotification();
                $notification->type = $request->notificationType;
                $notification->sender_id = $request->sender['id'];
                $notification->sender_name = $request->sender['name'];
                $notification->receiver_id = $deviceToken['user_id'];
                $notification->receiver_name = $deviceToken['user'];
                $notification->sender_role_id = $request->sender['role_id'];
                $notification->sender_role_name = $request->sender['role'];
                $notification->receiver_role_id = "";
                $notification->receiver_role_name = "";
                $notification->message_title = $request->notification['title'];
                $notification->message = $request->notification['body'];
                $notification->save();

            }
        }


        return $this->throwMessage(200, 'success', 'Push Notification Stored Successfully ', $notification);

    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $notificationId = $request->id;
        try {
            PushNotification::findOrFail($notificationId)->delete();
            return $this->throwMessage(200, 'success', 'Push Notification deleted successfully');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'Notification not found');
        }
    }

    public function allDestroy(Request $request)
    {
        try {
            auth()->user()->notifications()->delete();
            return $this->throwMessage(200, 'success', 'All Notifications deleted successfully');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'Notification not found');
        }
    }
}
