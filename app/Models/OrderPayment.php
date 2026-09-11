<?php
namespace App\Models;
use App\Enums\{OrderPaymentType,PaymentMethod,PaymentVerificationStatus};
use Illuminate\Database\Eloquent\{Concerns\HasUuids,Factories\HasFactory,Model};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderPayment extends Model {
 use HasFactory,HasUuids; protected $fillable=['reference_code','order_id','type','method','amount_irr','amount_currency','exchange_rate','receipt_number','proof_path','status','verified_by','verified_at','notes'];
 protected $casts=['type'=>OrderPaymentType::class,'method'=>PaymentMethod::class,'status'=>PaymentVerificationStatus::class,'amount_irr'=>'decimal:0','amount_currency'=>'decimal:4','exchange_rate'=>'decimal:4','verified_at'=>'datetime'];
 protected static function booted():void{static::creating(fn(OrderPayment $p)=>$p->reference_code??='PAY-'.now()->format('Ym').'-'.strtoupper(str()->random(4)));}
 public function order():BelongsTo{return $this->belongsTo(Order::class);} public function verifier():BelongsTo{return $this->belongsTo(User::class,'verified_by');}
}
