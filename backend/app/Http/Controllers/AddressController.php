<?php

namespace App\Http\Controllers;

use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    protected $service;
    public function __construct(AddressService $addressService)
    {
        $this->service = $addressService;
    }


    public function index( Request $request) 
    {
        return response()->json([
            'isSuccess' => true,
            'data' => $this->service->getByUserId($request->user()->id)
        ]);
    }
   public function store( Request $request) 
    {
   $data = array_merge($request->all(), [
        'user_id' => $request->user()->id,
    ]);

    return response()->json([
        'isSuccess' => true,
        'data' => $this->service->create($data)
    ]);
    }
   public function show($addressId) 
    {
        return response()->json([
            "isSuccess" => true,
            "data" => $this->service->getById($addressId)
        ]);
        // Logic to retrieve and return all addresses
    }
   public function update(Request $request, $addressId) 
    {
        return response()->json([
            'isSuccess' => true,
            'data' => $this->service->update($addressId, $request->all())
        ]);
    }
   public function destroy( $addressId) 
    {
        return response()->json([
            'isSuccess' => true,
            'data' => $this->service->delete($addressId)
        ]);
    }

}
