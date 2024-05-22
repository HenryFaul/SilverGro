<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationSeenController extends Controller
{
    //

    public function index(DatabaseNotification $notification): \Illuminate\Http\RedirectResponse
    {

        $notification->markAsRead();
        return back();
    }
}
