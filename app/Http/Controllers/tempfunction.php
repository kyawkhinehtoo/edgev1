private function getPriceForCustomerByIsp($customer_id)
    {
        $customer = Customer::find($customer_id);
    
        if (!$customer) {
            return null;
        }
    
        // Get how many valid customers (with smaller or equal ID) exist for the same ISP
        $position = Customer::join('status', 'status.id', 'customers.status_id')
            ->where('customers.isp_id', $customer->isp_id)
            ->where('customers.id', '<=', $customer->id)
            ->whereIn('status.type', ['active', 'suspense'])
            ->count();
    
        $bandwidth = (int) $customer->bandwidth;
    
        $selectedService = PortSharingService::where('max_speed', '>=', $bandwidth)
            ->orderBy('max_speed', 'asc')
            ->first();
    
        if (!$selectedService) {
            return null;
        }
        
        $tiers = json_decode($selectedService->rate, true);
       
        $fee = null;
        foreach ($tiers as $tier) {
            if ($position >= $tier['min']) {
                $fee = $tier['fees'];
            }
        }
    
        return [
            'fee' => $fee,
            'position' => $position,
        ];
    }