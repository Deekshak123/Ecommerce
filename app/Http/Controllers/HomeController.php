<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use App\Models\User;

use App\Models\Product;

use App\Models\Cart;

use App\Models\Order;


class HomeController extends Controller
{
    public function redirect(){

        $usertype=Auth::user()->usertype;

        if($usertype=='1'){

            return view('admin.home');

        }else{

            $data=product::paginate(3);

            $user=auth()->user();//we get user which is login currently

            $count=cart::where('phone',$user->phone)->count();//it will return the count of same phone number from cart table

     return view('user.home',compact('data','count')); 
        }
    }

    public function index(){

    if(Auth::id()){

        return redirect('redirect');
    }else{

        $data=product::paginate(3);

     return view('user.home',compact('data'));   
 }
    }

    public function search(Request $request){

        $search=$request->search;

        if($search==''){

            $data=product::paginate(3);

            return view('user.home',compact('data'));

        }

        $data=product::where('title','Like','%'.$search.'%')->get();

        return view('user.home',compact('data'));
    }

    public function addcart(Request $request, $id){

        if(Auth::id()){

            $user=auth()->user();//it stores particular user data

            $product=product::find($id);//it will find specific product id

            $cart=new cart;

            $cart->name=$user->name;//it will save particular user name in cart table

            $cart->phone=$user->phone;

            $cart->address=$user->address;

            $cart->product_title=$product->title;//it will save particular product name in cart table

            $cart->price=$product->price;

            $cart->quantity=$request->quantity;

            $cart->save();

            return redirect()->back()->with('message','Product Added Successfully');
        }
        else{

            return redirect('login');
        }

    }

    public function showcart(){

        $user=auth()->user();//we get user which is login currently

        $cart=cart::where('phone',$user->phone)->get();//to get product which specific user add into cart

        $count=cart::where('phone',$user->phone)->count();

        return view('user.showcart',compact('count','cart'));
    }


    public function deletecart($id){

        $data=cart::find($id);

        $data->delete();

        return redirect()->back()->with('message','Product Removed Successfully');
    }


    public function confirmorder(Request $request){

        $user=auth()->user();//we get user which is login currently

        $name=$user->name;

        $phone=$user->phone;

        $address=$user->address;

        foreach($request->productname as $key=>$productname){

            $order=new order;

            $order->product_name=$request->productname[$key];

            $order->quantity=$request->quantity[$key];

            $order->price=$request->price[$key];

            $order->name=$name;

            $order->phone=$phone;

            $order->address=$address;

            $order->status='not delivered';

            $order->save();

        }

        DB::table('carts')->where('phone',$phone)->delete();

        return redirect()->back()->with('message','Product Ordered Successfully');
    }
}
