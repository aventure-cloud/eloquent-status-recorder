<?php
namespace AventureCloud\EloquentStatusRecorder\Exceptions;

use AventureCloud\EloquentStatusRecorder\Models\Status;
use Illuminate\Database\Eloquent\Model;

class InvalidStatusChange extends \Exception
{
    /**
     * UndefinedStatusWasSet constructor.
     *
     * @param Model  $model
     * @param Status $oldStatus
     * @param string $newStatus
     */
    public function __construct(Model $model, Status $oldStatus, string $newStatus)
    {
        $modelName = class_basename($model);
        parent::__construct("Status of {$oldStatus->name} cannot be changed to {$newStatus} in {$modelName}");
    }
}