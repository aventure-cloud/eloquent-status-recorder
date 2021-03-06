<?php

namespace AventureCloud\EloquentStatusRecorder\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'statuses';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
