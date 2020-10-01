<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class item extends Model
{
  public $timestamps = false;
  protected $table = 'items';
  protected $fillable = ['artitle', 'price', 'discount_text', 'discount', 'lat', 'address', 'lng', 'suspensed', 'details', 'category_id', 'created_at'];
}
