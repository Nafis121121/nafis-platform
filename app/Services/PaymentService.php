<?php
namespace App\Services;
use App\Enums\{OrderPaymentType,PaymentStatus,PaymentVerificationStatus,OrderStatus};
use App\Models\{Order,OrderPayment};
use Illuminate\Support\Facades\DB;
class PaymentService {
 public function record(Order $order,array $data):OrderPayment{return DB::transaction(function()use($order,$data){$p=$order->payments()->create($data); if(($data['status']??'pending_verification')==='verified')$this->recalculate($order); return $p;});}
 public function verify(OrderPayment $payment,bool $accepted,?int $userId=null):void{DB::transaction(function()use($payment,$accepted,$userId){$payment->update(['status'=>$accepted?PaymentVerificationStatus::VERIFIED:PaymentVerificationStatus::REJECTED,'verified_by'=>$userId,'verified_at'=>now()]);if($accepted)$this->recalculate($payment->order);});}
 public function recalculate(Order $order):void{$paid=(float)$order->payments()->where('status',PaymentVerificationStatus::VERIFIED)->sum('amount_irr');$total=(float)$order->total_irr;$status=$paid<=0?PaymentStatus::UNPAID:($paid>=$total?PaymentStatus::FULLY_PAID:PaymentStatus::PARTIALLY_PAID);$order->update(['paid_irr'=>$paid,'balance_irr'=>max(0,$total-$paid),'payment_status'=>$status,'status'=>$paid>=(float)$order->required_deposit_irr&&$paid>0?OrderStatus::DEPOSIT_PAID:$order->status]);}
}
