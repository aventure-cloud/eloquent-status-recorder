<?php

namespace AventureCloud\EloquentStatusRecorder\Exceptions;

use Illuminate\Database\Eloquent\Model;

class UndefinedStatus extends \Exception
{
    /**
     * UndefinedStatus constructor.
     *
     * @param Model  $model
     * @param string $newStatus
     */
    public function __construct(Model $model, string $newStatus)
    {
        $modelName = class_basename($model);
        parent::__construct("Undefined status {$newStatus} was set for {$modelName}");
    }
}