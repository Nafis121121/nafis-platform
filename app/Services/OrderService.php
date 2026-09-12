<?php
namespace App\Services;
use App\Enums\{OrderStatus,PaymentStatus};
use App\Models\{Order,OrderItem,Quotation};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
class OrderService {
 public function __construct(private InventoryService $inventory){}
 public function convertQuotationToOrder(Quotation $quotation): Order {
  if($quotation->status->value!=='approved') throw new RuntimeException('Only approved quotations can become orders.');
  return DB::transaction(function()use($quotation){
   $order=Order::create(['user_id'=>$quotation->user_id,'assigned_to'=>$quotation->assigned_to,'quotation_id'=>$quotation->id,'status'=>OrderStatus::PENDING_PAYMENT,'base_currency'=>$quotation->base_currency,'exchange_rate'=>$quotation->exchange_rate,'subtotal_irr'=>$quotation->subtotal_irr,'tax_irr'=>$quotation->tax_irr,'total_irr'=>$quotation->final_total_irr,'required_deposit_irr'=>round($quotation->final_total_irr*.3),'balance_irr'=>$quotation->final_total_irr,'payment_status'=>PaymentStatus::UNPAID]);
   $warehouse=\App\Models\Warehouse::query()->where('is_active',true)->first();
   foreach($quotation->items as $item){$order->items()->create(['product_id'=>$item->product_id,'product_variant_id'=>$item->product_variant_id,'item_title'=>$item->item_title,'technical_description'=>$item->technical_description,'quantity'=>$item->quantity,'unit_price_currency'=>$item->unit_cost_currency,'unit_price_irr'=>$item->unit_price_irr,'total_irr'=>$item->total_price_irr]); if($warehouse && ($product=\App\Models\Product::find($item->product_id))){try{$this->inventory->reserve($warehouse,$product,$item->quantity,$item->product_variant_id?\App\Models\ProductVariant::find($item->product_variant_id):null);}catch(\RuntimeException $e){Log::warning('Inventory reservation failed while converting quotation to order.', ['quotation_id'=>$quotation->id,'order_id'=>$order->id,'product_id'=>$item->product_id,'variant_id'=>$item->product_variant_id,'quantity'=>$item->quantity,'error'=>$e->getMessage()]);}}}
   $quotation->update(['status'=>'converted_to_order']); return $order->load('items');
  });
 }
 public function cancel(Order $order):void{DB::transaction(function()use($order){$warehouse=\App\Models\Warehouse::query()->where('is_active',true)->first();foreach($order->items as $i){if($warehouse && ($product=\App\Models\Product::find($i->product_id))){try{$this->inventory->release($warehouse,$product,$i->quantity,$i->product_variant_id?\App\Models\ProductVariant::find($i->product_variant_id):null);}catch(\RuntimeException $e){Log::warning('Inventory release failed while cancelling order.', ['order_id'=>$order->id,'product_id'=>$i->product_id,'variant_id'=>$i->product_variant_id,'quantity'=>$i->quantity,'error'=>$e->getMessage()]);}}} $order->update(['status'=>OrderStatus::CANCELLED]);});}
}
