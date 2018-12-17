<?php

namespace App\Http\Controllers;

use App\Http\Resources\Item;
use Illuminate\Http\Request;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Item as ItemResource;
use App\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'state' => 'required|in:pending',
            'item_id' => 'required|integer|exists:items,id',
            'meal_id' => 'required|integer|exists:meals,id',
            'start' => 'required|date'
        ]);

        $order = new Order();
        $order->fill($request->all());
        $order->save();
        return response()->json(new OrderResource($order), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new OrderResource(Order::find($id));
    }

    /**
     * Display the specified resource.
     *
     * @param $mealId
     * @return \Illuminate\Support\Collection
     */
    public function mealItems($mealId)
    {
        $items = DB::table('items')
            ->join('orders', 'orders.item_id', '=', 'items.id')
            ->join('meals', 'meals.id', '=', 'orders.meal_id')
            ->where('meals.id', $mealId)
            ->select('items.*',
                'orders.state AS order_state',
                'orders.created_at AS order_created_at',
                'orders.updated_at AS order_updated_at',
                'orders.start AS order_start',
                'orders.end AS order_end')
            ->get();

        return $items;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'state' => 'required|in:pending,confirmed,in preparation,prepared,delivered,not delivered',
            'item_id' => 'required|integer|exists:items,id',
            'meal_id' => 'required|integer|exists:meals,id',
            'start' => 'required|date'
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->all());
        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }
}
