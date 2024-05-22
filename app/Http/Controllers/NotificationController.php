<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $notifications = Auth::user()->unreadNotifications()->paginate(10);

        return inertia(
            'Notifications/Index',
            [
                'notifications' => $notifications
            ]
        );
    }
}
