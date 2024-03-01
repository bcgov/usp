<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class TrackerTriggered
{
    use Dispatchable, SerializesModels;

    public $userGuid;
    public $userName;
    public $action;
    public $modelName;
    public $model;

    /**
     * Create a new event instance.
     */
    public function __construct($userGuid, $userName, $action, $modelName, $model)
    {
        $this->userGuid = $userGuid;
        $this->userName = $userName;
        $this->action = $action;
        $this->modelName = $modelName;
        $this->model = $model;
    }
}
