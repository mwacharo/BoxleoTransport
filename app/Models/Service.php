<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
  use HasFactory;

  protected $fillable = [

    'service_name',
  ];



  public function conditions()
  {
    return $this->hasMany(Condition::class);
  }

  public function vendor()
  {
    return $this->belongsTo(Vendor::class);
  }
  // public function fulltrack()
  // {
  //   return $this->hasMany(FullTrack::class);
  // }
}
