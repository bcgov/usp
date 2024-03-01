<?php

namespace App\Listeners;

use App\Events\TrackerTriggered;
use App\Models\Attestation;
use App\Models\Cap;
use App\Models\Institution;
use App\Models\Program;
use App\Models\Tracker;
use Illuminate\Support\Facades\Auth;

class StoreTracker
{
    /**
     * Handle the event.
     */
    public function handle(TrackerTriggered $event): void
    {
        $tracker = new Tracker();
        $tracker->user_guid = $event->userGuid;
        $tracker->user_name = $event->userName;
        $tracker->action = $event->action;
        $tracker->model_name = $event->modelName;
        $tracker->model_id = $event->model->id;
        $tracker->model_data = $event->model;
        $tracker->save();
    }
}
