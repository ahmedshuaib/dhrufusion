<?php

namespace TFMSoftware\DhruFusion\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class DhruFusion extends Model
{
    protected $table = 'dhru_fusions';

    public function user() {
        return $this->belongsTo(User::class);
    }
}
