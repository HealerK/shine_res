<?php

namespace App\Http\Controllers;

use App\Http\Requests\DishCreateRequest;
use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Http\Request;

class DishesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dishes = Dish::all();

        return view('kitchen.dish', [
            'dishes' => $dishes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('kitchen.dish_create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DishCreateRequest $request)
    {
        $dish = new Dish();
        $dish->name = $request->name;
        $dish->category_id = $request->category;
        $imageName = date('YmdHis') . "." . request()->dish_image->getClientOriginalExtension();
        request()->dish_image->move(public_path('images'), $imageName);
        $dish->image = $imageName;
        $dish->save();
        return redirect('dish')->with('message', 'You created a delicious dish successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Dish $dish)
    {
        $categories = Category::all();
        return view('kitchen.dish_edit', [
            'dish' => $dish,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dish $dish)
    {
        request()->validate([
            'name' => 'required',
            'category' => 'required'
        ]);
        $dish->name = $request->name;
        $dish->category_id = $request->category;
        if ($request->dish_image) {
            $imageName = date('YmdHis') . "." . request()->dish_image->getClientOriginalExtension();
            request()->dish_image->move(public_path('images'),$imageName);
            $dish->image = $imageName;
        }
        $dish->save();

        return redirect('/dish')->with('message','You edit dish.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dish $dish)
    {
        $dish->delete();

        return redirect('/dish')->with('message','Delete dish successfully!');
    }

    public function order()
    {
        $rawStatus = config('res.status');
        $status = array_flip($rawStatus);
        $orders = Order::whereIn('status',[1,2])->get();
        return view('kitchen.order',[
            'orders' => $orders,
            'status' => $status
        ]);
    }

    public function approve(Order $order)
    {
        $order->status = config('res.status.processing');
        $order->save();
        return redirect('/order')->with('message','Order Successfully!');
    }

    public function cancel(Order $order)
    {
        $order->status = config('res.status.cancel');
        $order->save();
        return redirect('/order')->with('message','Order Rejected!');
    }

    public function ready(Order $order)
    {
        $order->status = config('res.status.ready');
        $order->save();
        return redirect('/order')->with('message','Order Ready!');
    }
}
