<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    public function getAll() {
        return Address::all();
    }

    public function getById($id) {
        return Address::find($id);
    }

    public function create(array $data) {
        return Address::create($data);
    }

    public function update($id, array $data) {
        $Address = Address::findOrFail($id);
        $Address->update($data);
        return $Address;
    }

    public function delete($id) {
        $Address = Address::findOrFail($id);
        $Address->delete();
        return true;
    }
}
