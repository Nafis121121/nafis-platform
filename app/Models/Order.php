<?php
namespace App\Models;
use App\Enums\{OrderStatus,PaymentStatus};
use Illuminate\Database\Eloquent\{Concerns\HasUuids,Factories\HasFactory,Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class Order extends Model {
 use HasFactory,HasUuids;
 protected $fillable=['reference_code','user_id','assigned_to','quotation_id','status','base_currency','exchange_rate','subtotal_irr','discount_irr','tax_irr','late_penalty_irr','total_irr','required_deposit_irr','paid_irr','balance_irr','payment_status','delivery_address','incoterms','shipping_terms','customer_notes','internal_notes'];
 protected $casts=['status'=>OrderStatus::class,'payment_status'=>PaymentStatus::class,'exchange_rate'=>'decimal:4','subtotal_irr'=>'decimal:0','discount_irr'=>'decimal:0','tax_irr'=>'decimal:0','late_penalty_irr'=>'decimal:0','total_irr'=>'decimal:0','required_deposit_irr'=>'decimal:0','paid_irr'=>'decimal:0','balance_irr'=>'decimal:0'];
 protected static function booted(): void { static::creating(fn(Order $o)=>$o->reference_code??='ORD-'.now()->format('Ym').'-'.strtoupper(str()->random(4))); }
 public function customer(): BelongsTo{return $this->belongsTo(User::class,'user_id');}
 public function assignee(): BelongsTo{return $this->belongsTo(User::class,'assigned_to');}
 public function quotation(): BelongsTo{return $this->belongsTo(Quotation::class);}
 public function items(): HasMany{return $this->hasMany(OrderItem::class);}
 public function payments(): HasMany{return $this->hasMany(OrderPayment::class);}
 public function shipments(): HasMany{return $this->hasMany(Shipment::class);}
}
