<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Http\Traits\GlobalTraits;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    use GlobalTraits;

    protected $model = 'Notification';

    protected $limit = 10;  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {


            $listData =  auth()->user()->notifications();
           
            if ($request->has('start_date') && $request->has('end_date')) {
                $listData = $listData->whereBetween('created_at', [$request->start_date, $request->end_date]);
            } 

            $listData = $listData->orderBy('read_at', 'asc');
            $listData = $listData->orderBy('created_at', 'desc');

            $listData = $listData->paginate($this->limit);
            return NotificationResource::collection($listData);

          //  return $this->throwMessage(200, 'success', 'All the list of notifications ', $listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
        

    }


    public function unreadNotifications(){
        try {

            $unreadNotifications = auth()->user()->unreadnotifications();

            $unreadNotifications = $unreadNotifications->orderBy('read_at', 'asc');
            $unreadNotifications = $unreadNotifications->orderBy('created_at', 'desc');

            $unreadNotifications = $unreadNotifications->get();

            return $this->throwMessage(200, 'success', 'All the list of unread notifications ', $unreadNotifications);


        } catch (\Exception $e) {
            return $this->throwMessage(413,'error',$e->getMessage());   
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
            return $this->throwMessage(413,'error',$e->getMessage());   
        }

        
    }


    public function read(Request $request)
    {
        $id = $request->id;

        try {

            $notification = auth()->user()->notifications->where('id', $id)->markAsRead();
            return $this->throwMessage(200, 'success', 'Notification data ', $notification);

        } catch (\Exception $e) {
            return $this->throwMessage(413,'error',$e->getMessage());   
        }

        
    }

    public function allNotificationRead(Request $request)
    {

        try {

            auth()->user()->notifications->markAsRead();
            return $this->throwMessage(200, 'success', 'All Notification Mark As Read ');

        } catch (\Exception $e) {
            return $this->throwMessage(413,'error',$e->getMessage());   
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
        try {

            //dd($request->all());
            
            return $this->throwMessage(200, 'success', 'Notification data ', $request->all());

        } catch (\Exception $e) {
            return $this->throwMessage(413,'error',$e->getMessage());   
        }
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
            Notification::findOrFail($notificationId)?->delete();
            return $this->throwMessage(200, 'success', 'Notification deleted successfully');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'Notification not found');
        }
    }

    public function allDestroy(Request $request)
    {
        try {
            auth()->user()->notifications->delete();
            return $this->throwMessage(200, 'success', 'All Notifications deleted successfully');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'Notification not found');
        }
    }

}
