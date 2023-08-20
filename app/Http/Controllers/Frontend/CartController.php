<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function cart(){
        return view('frontend.cart');
    }

    public function add(Product $product, Request $request){
        $validated = $request->validate([
            'size' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);
        $size = $validated['size'];
        $quantity = $validated['quantity'];
        $cart = session('cart', []);

        if(array_key_exists($product->id, $cart) 
        && array_key_exists($size, $cart[$product->id])){
            $cart[$product->id][$size]['quantity'] += $quantity;
            
        }
        else{
            $cart[$product->id][$size] = [
                'product_id' => $product->id,
                'image' => $product->images->first()->image,
                'name' => $product->name,
                'quantity' => $quantity,
                'size' => $validated['size'],
                'price'=> $product->price,
            ];
        }
        session()->put('cart', $cart);
        $this->totalPrice();
        return redirect()->back()->with('success', 'Thêm sản phẩm vào giỏ hàng thành công!');
    }

    public function delete($product_id, $size){
        // return ('cart.'.$product_id .'.' .$size);
        session()->pull('cart.'.$product_id.'.'.$size );
        $this->totalPrice();
        return back()->with('success', 'Xóa sản phẩm khỏi giỏ hành thành công!');
    }

    protected function totalPrice(){
        $total_price = 0;
        foreach(session('cart') as $carts){
            foreach($carts as $item){
                $total_price += $item['quantity'] * $item['price'];
            }
        }
        session()->put('total_price', $total_price);
    }
}