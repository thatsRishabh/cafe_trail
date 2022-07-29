<?php

use App\Models\Unit;
use App\Models\ProductStockManage;
use App\Models\ProductMenu;
use App\Models\ProductInfo;
use App\Models\EmployeeAttendence;
use App\Models\AttendenceList;
use App\Models\Order;
use App\Models\User;

function prepareResult($error, $data, $msg)
{
	return ['success' => $error, 'data' => $data, 'message' => $msg];
}

function unitConversion($unitID, $quantity) {

	$unitName = Unit::where('id', $unitID)->get('name')->first();
	// return $unitName->name;
   
	if((strtolower($unitName->name) == "kilogram") || (strtolower($unitName->name) == "liter"))
	    {
	        $value = $quantity*1000;
	        return $value;
	    }
	    elseif ((strtolower($unitName->name) == "gram") || (strtolower($unitName->name) == "millilitre") || (strtolower($unitName->name) == "piece/pack")) 
	    {
			$value = $quantity;
	        return $value;
	    }
		elseif ((strtolower($unitName->name) == "dozen")) 
	    {
			$value = $quantity*12;
	        return $value;
	    }

}


	function getLast30TotalSale()
		{
			$today     = new \DateTime();
			$begin     = $today->sub(new \DateInterval('P30D'));
			$end       = new \DateTime();
			$end       = $end->modify('+1 day');
			$interval  = new \DateInterval('P1D');
			$daterange = new \DatePeriod($begin, $interval, $end);
			$totalSale =[];
			foreach ($daterange as $date) {
				
			$salesSum = Order::whereDate('created_at',$date->format('Y-m-d'))->sum('netAmount'); 
				
				$totalSale[] = $salesSum;
			}
		

			// $data = implode(', ', $totalSale);
			return $totalSale;
		}

	function getLast30TotalCustomer()
		{
			$today     = new \DateTime();
			$begin     = $today->sub(new \DateInterval('P30D'));
			$end       = new \DateTime();
			$end       = $end->modify('+1 day');
			$interval  = new \DateInterval('P1D');
			$daterange = new \DatePeriod($begin, $interval, $end);
			$totalCustomer =[];
			foreach ($daterange as $date) {
				
			$customer = Order::whereDate('created_at',$date->format('Y-m-d'))->count(); 
				
				$totalCustomer[] = $customer;
			}
		

			// $data = implode(', ', $totalCustomer);
			return $totalCustomer;
		}

	function getLast30TotalProduct()
		{
			$today     = new \DateTime();
			$begin     = $today->sub(new \DateInterval('P30D'));
			$end       = new \DateTime();
			$end       = $end->modify('+1 day');
			$interval  = new \DateInterval('P1D');
			$daterange = new \DatePeriod($begin, $interval, $end);
			$totalProduct =[];
			foreach ($daterange as $date) {
				
			$productSum = Order::whereDate('created_at',$date->format('Y-m-d'))->sum('cartTotalQuantity'); 
				
				$totalProduct[] = $productSum;
			}
		

			// $data = implode(', ', $totalProduct);
			return $totalProduct;
		}

	function getLast30TotalRevenue()
		{
			$today     = new \DateTime();
			$begin     = $today->sub(new \DateInterval('P30D'));
			$end       = new \DateTime();
			$end       = $end->modify('+1 day');
			$interval  = new \DateInterval('P1D');
			$daterange = new \DatePeriod($begin, $interval, $end);
			$totalRevenue =[];
			foreach ($daterange as $date) {
				
				$revenueSum = Order::whereDate('created_at',$date->format('Y-m-d'))->sum('netAmount'); 
				
				$totalRevenue[] = $revenueSum;
			}
		

			// $data = implode(', ', $totalRevenue);
			return $totalRevenue;
		}


	function getLast30DaysList()
		{
			$today     = new \DateTime();
			$begin     = $today->sub(new \DateInterval('P30D'));
			$end       = new \DateTime();
			$end       = $end->modify('+1 day');
			$interval  = new \DateInterval('P1D');
			$daterange = new \DatePeriod($begin, $interval, $end);
			foreach ($daterange as $date) {
				$dateList[] = ''.$date->format("Y-m-d").'';
			}
			
			return $dateList;
		}
