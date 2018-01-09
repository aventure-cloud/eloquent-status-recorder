<?php
namespace AventureCloud\EloquentStatusRecorder\Traits;

use AventureCloud\EloquentStatusRecorder\Events\StatusChanged;
use AventureCloud\EloquentStatusRecorder\Events\StatusChanging;
use AventureCloud\EloquentStatusRecorder\Exceptions\InvalidStatusChange;
use AventureCloud\EloquentStatusRecorder\Exceptions\UndefinedStatus;
use AventureCloud\EloquentStatusRecorder\Models\Status;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasStatus
{
    /**
     * Get last status as attribute
     *
     * @return mixed
     */
    public function getStatusAttribute()
    {
        //return name of the latest status
        return optional($this->lastStatus)->name;
    }

    /**
     * Set new status
     *
     * @param string $status
     * @throws InvalidStatusChange
     * @throws UndefinedStatus
     */
    public function setStatusAttribute(string $status)
    {
        if ($this->status === $status) {
            return;
        }
        if (!$this->canBe($status)) {
            throw new InvalidStatusChange($this, $this->status, $status);
        }

        event(new StatusChanging($status, $this));

        $this->runOnChangeCallback($status);
        $this->updateStatus($status);

        event(new StatusChanged($status, $this));
    }

    /**
     * Update model's status history
     *
     * @param mixed $status
     * @return mixed
     */
    public function updateStatus($status)
    {
        //if model not yet exist, return null
        if(!$this->exists){
            return null;
        }

        if($status instanceof Status){
            return $this->statusHistory()->create($status);
        }

        return $this->statusHistory()->create(['name' => $status]);
    }

    /**
     * Check if given status is valid applying roles
     *
     * @param $status
     * @return bool
     * @throws UndefinedStatus
     */
    public function canBe($status): bool
    {
        $this->throwExceptionIfStatusInvalid($status);
        return $this->checkFrom($status) && $this->checkNotFrom($status);
    }

    /**
     * Run callback on every new status value
     *
     * @param string $status
     */
    private function runOnChangeCallback(string $status)
    {
        $method = "on".studly_case($status);
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    /**
     * Generate exception is status is invalid
     *
     * @param string $status
     * @throws UndefinedStatus
     */
    private function throwExceptionIfStatusInvalid(string $status)
    {
        if (!array_key_exists($status, $this->statuses)) {
            throw new UndefinedStatus($this, $status);
        }
    }

    /**
     * Apply FROM restrictions
     *
     * @param $status
     * @return bool
     */
    private function checkFrom($status): bool
    {
        if (!array_key_exists('from', $this->statuses[$status])) {
            return true;
        }

        $from = $this->statuses[$status]['from'];

        if (is_string($from)) {
            return $this->status === $from;
        }

        foreach ($from as $toOption) {
            if ($this->status === $toOption) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apply NOT-FROM restrictions
     *
     * @param $status
     * @return bool
     */
    private function checkNotFrom($status): bool
    {
        if (!array_key_exists('not-from', $this->statuses[$status])) {
            return true;
        }

        $from = $this->statuses[$status]['not-from'];

        if (is_string($from)) {
            return $this->status !== $from;
        }

        foreach ($from as $toOption) {
            if ($this->status === $toOption) {
                return false;
            }
        }

        return true;
    }

    /**
     * All status history
     *
     * @return MorphMany
     */
    public function statusHistory() : MorphMany
    {
        return $this->morphMany(config('eloquent-status-recorder.status_model'), 'statusable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Current status
     *
     * @return MorphOne
     */
    public function lastStatus() : MorphOne
    {
        return $this->morphOne(config('eloquent-status-recorder.status_model'), 'statusable')
            ->orderBy('created_at', 'desc');
    }
}