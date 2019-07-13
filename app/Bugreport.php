<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bugreport extends Model
{
    protected $fillable = [
        'name',
        'email',
        'title',
        'priority',
        'description',
        'url',
        'status',
    ];

    public function getPriority(){
        switch ($this->priority){
            case 0:
                return __('user.bugreport.prioritySelect.low');
            case 1:
                return __('user.bugreport.prioritySelect.normal');
            case 2:
                return __('user.bugreport.prioritySelect.high');
            case 3:
                return __('user.bugreport.prioritySelect.critical');
        }
    }

    public function getPriorityBadge(){
        switch ($this->priority){
            case 0:
                return '<span class="badge badge-info">'.$this->getPriority().'</span>';
            case 1:
                return '<span class="badge badge-primary">'.$this->getPriority().'</span>';
            case 2:
                return '<span class="badge badge-warning">'.$this->getPriority().'</span>';
            case 3:
                return '<span class="badge badge-danger">'.$this->getPriority().'</span>';
        }
    }

    public function getStatus(){
        switch ($this->status){
            case 0:
                return __('cruds.bugreport.statusSelect.open');
            case 1:
                return __('cruds.bugreport.statusSelect.inprogress');
            case 2:
                return __('cruds.bugreport.statusSelect.resolved');
            case 3:
                return __('cruds.bugreport.statusSelect.close');
        }
    }

    public function getStatusBadge(){
        switch ($this->status){
            case 0:
                return '<span class="badge badge-dark">'.$this->getStatus().'</span>';
            case 1:
                return '<span class="badge badge-primary">'.$this->getStatus().'</span>';
            case 2:
                return '<span class="badge badge-light">'.$this->getStatus().'</span>';
            case 3:
                return '<span class="badge badge-success">'.$this->getStatus().'</span>';
        }
    }
}
