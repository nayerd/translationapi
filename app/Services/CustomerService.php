<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    /**
     * Creates a new customer object
     *
     * @param string|null $customerId
     * @return Customer|null
     */
    public function createCustomer(string $customerId = null): ?Customer
    {
        if (empty($customerId)) {
            return null;
        }

        $customer = Customer::whereCustomerId($customerId)->first();
        if (!empty($customer)) {
            return $customer;
        }

        return Customer::updateOrCreate([
            'customer_id'    => $customerId
        ]);
    }
}
