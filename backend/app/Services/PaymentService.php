<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    public function getAll() {
        return Payment::all();
    }

    public function getById($id) {
        return Payment::find($id);
    }

    public function create(array $data) {
        return Payment::create($data);
    }

    public function update($id, array $data) {
        $Payment = Payment::findOrFail($id);
        $Payment->update($data);
        return $Payment;
    }

    public function delete($id) {
        $Payment = Payment::findOrFail($id);
        $Payment->delete();
        return true;
    }
}
