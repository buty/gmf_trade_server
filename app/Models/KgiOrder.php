<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KgiOrder extends Model {

    protected $connection = 'trade';

    protected $table = 'kgi_order_list';

    //protected $fillable = array("order_status");

    public $timestamps = false;

}
