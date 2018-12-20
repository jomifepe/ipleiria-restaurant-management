<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Meal as MealResource;
use App\Meal;
use Illuminate\Support\Facades\DB;

class MealControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return MealResource::collection(Meal::all());
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
            'table_number' => 'required|exists:restaurant_tables',
            'state' => 'required|in:active,terminated,paid,not paid',
            'start' => 'required|date',
            'responsible_waiter_id' => 'required|integer|exists:users,id'
        ]);
        $activeMeal = Meal::where('table_number', $request->table_number)
            ->where('state', 'active')->first();
        if (!empty($activeMeal)) {
            return response()->json([
                'message' => 'Table already has an active meal associated',
                'status' => 422
            ], 422);
        }

        $meal = new Meal();
        $meal->fill($request->all());
        $meal->save();
        return response()->json(new MealResource($meal), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return MealResource
     */
    public function show($id)
    {
        return new MealResource(Meal::find($id));
    }

    public function tableMeal($tableNumber) {
        return new MealResource(Meal::where('table_number', $tableNumber)->first());
    }

    /**
     * Display the specified resource.
     *
     * @param $waiterId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function responsible($waiterId)
    {
        return MealResource::collection(Meal::where('responsible_waiter_id', $waiterId)->get());
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
            'table_number' => 'exists:restaurant_tables',
            'state' => 'in:active,terminated,paid,not paid',
            'start' => 'date',
            'responsible_waiter_id' => 'integer|exists:users,id'
        ]);

        $meal = Meal::findOrFail($id);
        $meal->update($request->all());
        return new MealResource($meal);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meal = Meal::findOrFail($id);
        $meal->delete();
        return response()->json(null, 204);
    }
}
