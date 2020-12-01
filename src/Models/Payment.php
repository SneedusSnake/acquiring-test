<?php
namespace Sneedus\Acquiring\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "payment_id",
        "status"
    ];
}