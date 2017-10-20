<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $table = "assignment__c";

    protected $dates = ["start_date","end_date"];

    protected $appends = ['start_date_human'];

    public function getStartDateHumanAttribute()
    {
        return $this->start_date->diffForHumans();
    }
}
