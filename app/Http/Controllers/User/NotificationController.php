<?php

namespace App\Http\Controllers\User;

use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function unread()
    {
        return response()->json(Auth::user()->unreadNotifications->sortByDesc('created_at'));
    }

    public function update(Request $request)
    {
        DB::table('notifications')
            ->where('id', $request->input('id'))
            ->update(['read_at' => Carbon::now()]);
    }

    public function all()
    {
        $user = Auth::user();
        if($user){

            return response()->json([
              'unreadCount' => $user->unreadNotifications()->count(),
              'notifications' => $user->notifications->sortByDesc('created_at')
            ]);
        }

    }

    public function markAllAsRead()
    {
      Auth::user()->unreadNotifications->markAsRead();
      return response()->json(Auth::user()->notifications);
    }
}
