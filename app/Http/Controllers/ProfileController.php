<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateBillingInfoRequest;
use App\Http\Requests\Profile\UpdateInfoRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Profile\UpdateInfoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInfoRequest $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(["message" => "User is not found"], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone ?? null;
        $user->update();

        return response()->json(new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update the user password.
     * @param  \App\Http\Requests\Profile\UpdatePasswordRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePasswordRequest $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(["message" => "User is not found"], 404);
        }

        $user->password = Hash::make($request->new_password);

        $user->update();

        return response()->json(new UserResource($user));
    }

    /**
     * Update the user's billing information.
     * @param  \App\Http\Requests\Profile\UpdateBillingInfoRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateBillingInfo(UpdateBillingInfoRequest $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(["message" => "User is not found"], 404);
        }

        if ($request->has('address_id')) {
            $address = $user->addresses()->find($request->address_id);

            if (is_null($address)) {
                return response()->json(["message" => "Address is not found"], 404);
            }
        } else {
            $address = new Address();
        }
        
        $address->address = $request->address;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->postal_code = $request->postal_code;
        
        if ($request->has('address_id')) {
            $address->update();
        } else {
            $address->save();
            $user->addresses()->attach($address->id);
        }

        return response()->json(['success' => true]);
    }
}
