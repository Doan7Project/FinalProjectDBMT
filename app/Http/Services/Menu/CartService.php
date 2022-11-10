<?php

namespace App\Http\Services\Menu;

use App\Jobs\SendMail;
use App\Models\Customer;
use App\Models\myOrder;
use App\Models\order_detail;
use App\Models\order_master;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * Class CartService
 * @package App\Services
 */
class CartService
{
    public function create($request)
    {
        $qty = (int)$request->input('num_product');
        $product_id = (int)$request->input('product_id');

        if ($qty <= 0 || $product_id <= 0) {
            Session::flash('error', 'Incorrect Quantity or Product');
            return false;
        }

        $carts = Session::get('carts');
    
        
        if (is_null($carts)) {
            Session::put('carts', [
                $product_id => $qty
            ]);
            return true;
        }
    
        $exists = Arr::exists($carts, $product_id);
        if ($exists) {
            $carts[$product_id] = $carts[$product_id] + $qty;
            Session::put('carts', $carts);
            return true;
        }

        $carts[$product_id] = $qty;
        Session::put('carts', $carts);

        return true;
        
    }

    public function getProduct()
    {
        $carts = Session::get('carts');
        if (is_null($carts)) return [];
        
        $productId = array_keys($carts);
        return Product::select('id', 'product_name', 'price', 'images')
            ->where('status', 'Active')
            ->whereIn('id', $productId)
            ->get();
            
        // dd($productId);
    }
    
    public function update($request)
    {
        Session::put('carts', $request->input('num_product'));

        return true;
    }

    public function remove($id)
    {
        $carts = Session::get('carts');
        unset($carts[$id]);

        Session::put('carts', $carts);
        return true;
    }

    public function addCart($request)
    {
        try {
        //     DB::beginTransaction();

            $carts = Session::get('carts');

            if (is_null($carts))
                return false;

            $customerId = session('LoggedUserid');
            $unique_id = 'FNI-' . floor(time() - 999999999);
            
            $order = order_master::create([
                'customer_id'       => $customerId,
                'date_required'     => $request->input('txtDateOrder'),
                'order_number'      => $unique_id,
                'notes'             => $request->input('txtNote')
            ]);
            // dd("$order->id");
            $this->infoProductCart($carts, $order->id);
            
            // DB::commit();
            Session::flash('success', 'Order Successfulyl');

            #Queue
            // SendMail::dispatch()->delay(now()->addSeconds(5));
            
            Session::forget('carts');
        } catch (\Exception $err) {
        //     DB::rollBack();
            Session::flash('error', 'Order Error, Please try again later');
            return false;
        }

        return true;
    }

    protected function infoProductCart($carts, $orderId)
    {
        $productId = array_keys($carts);
        $products = Product::select('id', 'product_name', 'price', 'images')
            ->whereIn('id', $productId)
            ->get();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'product_id'    => $product->id,
                'order_id'      => $orderId,
                'quantity'      => $carts[$product->id],
            ];
        }
    return order_detail::insert($data);
    }

    public function getCustomer()
    {
    return Customer::orderByDesc('id')->paginate(15);
    }

    public function getProductForCart($customer)
    {
        return $customer->carts()->with(['product' => function ($query) {
            $query->select('id', 'name', 'images');
        }])->get();
    }
    
    public function myOrder(){
        $customerId = session('LoggedUserid');
        $myOrder = myOrder::select('order_number','order_id','created_at','product_id','quantity','price','images','product_name')
            ->where('customer_id',$customerId)
            ->get();
        $data = [];
        foreach($myOrder as $items) {
            $data [] = [
                'order_number'    => $items->order_number,
                'order_id'    => $items->order_id,
                'created_at' => $items->created_at, 
                'product_id'    => $items->product_id,
                'quantity'    => $items->quantity,
                'product_name'    => $items->product_name,
                'price'    => $items->price,
                'images'    => $items->images,
            ];
        }
        // // dd($data);
        return $data;
    }

    public function numOrder(){
        $customerId = session('LoggedUserid');
        $myOrder = myOrder::select('order_number')
            ->where('customer_id',$customerId)
            ->groupby('order_number')
            ->get();
        $data = [];
        foreach($myOrder as $items) {
            $data [] = [
                'order_number'    => $items->order_number,
                // 'created_at' => $items->created_at,
            ];
        }
        // // dd($data);
        return $data;
    }
}