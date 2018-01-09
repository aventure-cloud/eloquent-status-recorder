<?php
namespace AventureCloud\EloquentStatusRecorder\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class StatusChanged
{
    use SerializesModels;

    /**
     * @var string
     */
    public $status;

    /**
     * @var Model
     */
    public $model;

    /**
     * Create a new event instance.
     *
     * @param $status
     * @param Model $model
     */
    public function __construct($status, Model $model)
    {
        $this->status = $status;
        $this->model = $model;
    }
}