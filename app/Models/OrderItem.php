<?php
namespace App\Models;
use Illuminate\Database\Eloquent\{Concerns\HasUuids,Factories\HasFactory,Model};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderItem extends Model {
 use HasFactory,HasUuids; protected $fillable=['order_id','product_id','product_variant_id','item_title','technical_description','part_number','quantity','unit_price_currency','unit_price_irr','total_irr'];
 protected $casts=['quantity'=>'integer','unit_price_currency'=>'decimal:4','unit_price_irr'=>'decimal:0','total_irr'=>'decimal:0'];
 public function order():BelongsTo{return $this->belongsTo(Order::class);} public function product():BelongsTo{return $this->belongsTo(Product::class);} public function variant():BelongsTo{return $this->belongsTo(ProductVariant::class,'product_variant_id');}
}
