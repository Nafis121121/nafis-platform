<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class WholesaleRule extends Model {
 protected $fillable=['product_id','category_id','min_quantity','max_quantity','discount_percentage','starts_at','ends_at','is_active'];
 protected $casts=['discount_percentage'=>'decimal:4','starts_at'=>'date','ends_at'=>'date','is_active'=>'boolean'];
 public function product():BelongsTo{return $this->belongsTo(Product::class);} public function category():BelongsTo{return $this->belongsTo(Category::class);}
}
