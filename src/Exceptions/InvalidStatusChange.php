<?php
namespace AventureCloud\EloquentStatusRecorder\Exceptions;

use Illuminate\Database\Eloquent\Model;

class InvalidStatusChange extends \Exception
{
    /**
     * UndefinedStatusWasSet constructor.
     *
     * @param Model  $model
     * @param string $oldStatus
     * @param string $newStatus
     */
    public function __construct(Model $model, string $oldStatus, string $newStatus)
    {
        $modelName = class_basename($model);
        parent::__construct("Status of {$oldStatus} cannot be changed to {$newStatus} in {$modelName}");
    }
}