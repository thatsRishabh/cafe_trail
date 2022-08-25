<?php

use App\Models\Unit;
use App\Models\ProductStockManage;
use App\Models\ProductMenu;
use App\Models\ProductInfo;
use App\Models\EmployeeAttendence;
use App\Models\AttendenceList;
use App\Models\Category;
use App\Models\OrderContain;
use App\Models\Expense;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

function imageBaseURL() {

	        // return "http://192.168.1.25:8000/";
			return "https://backend.gofactz.com/public/";

}

	function getLast30TotalSale($day, $startDate , $endDate)
		{
			if(!empty($day))
            {

				$today     = new \DateTime();
				// $begin     = $today->sub(new \DateInterval('P30D'));
	
				if(($day == 1 ))
				{
					$begin = $today->sub(new \DateInterval('P0D'));
				}
				elseif (($day == 7)) 
				{
					$begin= $today->sub(new \DateInterval('P7D'));
				}
				elseif (($day == 30 )) 
				{
					$begin= $today->sub(new \DateInterval('P30D'));
				}
	
				$end       = new \DateTime();
				$end       = $end->modify('+1 day');
				$interval  = new \DateInterval('P1D');
				$daterange = new \DatePeriod($begin, $interval, $end);
				$totalSale =[];
				foreach ($daterange as $date) {
					
				$salesSum = Order::whereDate('created_at',$date->format('Y-m-d'))->sum('netAmount'); 
					
					$totalSale[] = $salesSum;
				}
			}

		
			if(!empty( $startDate))
            {

				$rangArray = []; 
				$startDate = strtotime($startDate);
				$endDate = strtotime($endDate);
					
					for ($currentDate = $startDate; $currentDate <= $endDate; 
													$currentDate += (86400)) {
															
						$date = date('Y-m-d', $currentDate);
						$rangArray[] = $date;
					}
  			
				$totalSale =[];
				foreach ($rangArray as $date) {
					
					$salesSum = Order::whereDate('created_at',$date)->sum('netAmount'); 
					
					$totalSale[] = $salesSum;
				}
		
			}
		
		

			// $data = implode(', ', $totalSale);
			return $totalSale;
		}

	// function getLast30TotalCustomer($day)
	// 	{
	// 		$today     = new \DateTime();
	// 		// $begin     = $today->sub(new \DateInterval('P30D'));

	// 		if(($day == 1 ))
	// 		{
	// 			$begin = $today->sub(new \DateInterval('P0D'));
	// 		}
	// 		elseif (($day == 7)) 
	// 		{
	// 			$begin= $today->sub(new \DateInterval('P7D'));
	// 		}
	// 		elseif (($day == 30 )) 
	// 		{
	// 			$begin= $today->sub(new \DateInterval('P30D'));
	// 		}

	// 		$end       = new \DateTime();
	// 		$end       = $end->modify('+1 day');
	// 		$interval  = new \DateInterval('P1D');
	// 		$daterange = new \DatePeriod($begin, $interval, $end);
	// 		$totalCustomer =[];
	// 		foreach ($daterange as $date) {
				
	// 		$customer = Order::whereDate('created_at',$date->format('Y-m-d'))->count(); 
				
	// 			$totalCustomer[] = $customer;
	// 		}
		

	// 		// $data = implode(', ', $totalCustomer);
	// 		return $totalCustomer;
	// 	}

	function getLast30TotalProduct($day, $startDate , $endDate)
		{
			if(!empty($day))
            {
				$today     = new \DateTime();
				// $begin     = $today->sub(new \DateInterval('P30D'));
	
				if(($day == 1 ))
				{
					$begin = $today->sub(new \DateInterval('P0D'));
				}
				elseif (($day == 7)) 
				{
					$begin= $today->sub(new \DateInterval('P7D'));
				}
				elseif (($day == 30 )) 
				{
					$begin= $today->sub(new \DateInterval('P30D'));
				}
	
				$end       = new \DateTime();
				$end       = $end->modify('+1 day');
				$interval  = new \DateInterval('P1D');
				$daterange = new \DatePeriod($begin, $interval, $end);
				$totalProduct =[];
				foreach ($daterange as $date) {
					
				$productSum = Order::whereDate('created_at',$date->format('Y-m-d'))->sum('cartTotalQuantity'); 
					
					$totalProduct[] = $productSum;
				}
			
		
			}

			if(!empty( $startDate))
            {

				$rangArray = []; 
				$startDate = strtotime($startDate);
				$endDate = strtotime($endDate);
					
					for ($currentDate = $startDate; $currentDate <= $endDate; 
													$currentDate += (86400)) {
															
						$date = date('Y-m-d', $currentDate);
						$rangArray[] = $date;
					}
  			
				$totalProduct =[];
				foreach ($rangArray as $date) {
					
					$productSum = Order::whereDate('created_at',$date)->sum('cartTotalQuantity'); 
					
					$totalProduct[] = $productSum;
				}
		
			}
		

			// $data = implode(', ', $totalProduct);
			return $totalProduct;
		}

	function getLast30TotalExpense($day, $startDate , $endDate)
		{
			if(!empty($day))
            {

						$today     = new \DateTime();
					// // $begin     = $today->sub(new \DateInterval('P30D'));

					if(($day == 1 ))
					{
						$begin = $today->sub(new \DateInterval('P0D'));
					}
					elseif (($day == 7)) 
					{
						$begin= $today->sub(new \DateInterval('P7D'));
					}
					elseif (($day == 30 )) 
					{
						$begin= $today->sub(new \DateInterval('P30D'));
					}

					$end       = new \DateTime();
					$end       = $end->modify('+1 day');
					$interval  = new \DateInterval('P1D');
					$daterange = new \DatePeriod($begin, $interval, $end);
					
					$totalExpense =[];
					foreach ($daterange as $date) {
						
						$expenseSum = Expense::whereDate('created_at',$date->format('Y-m-d'))->sum('totalExpense'); 
						
						$totalExpense[] = $expenseSum;
					}

			}

			if(!empty( $startDate))
            {

				$rangArray = []; 
				$startDate = strtotime($startDate);
				$endDate = strtotime($endDate);
					
					for ($currentDate = $startDate; $currentDate <= $endDate; 
													$currentDate += (86400)) {
															
						$date = date('Y-m-d', $currentDate);
						$rangArray[] = $date;
					}
  			
				$totalExpense =[];
				foreach ($rangArray as $date) {
					
					$expenseSum = Expense::whereDate('created_at',$date)->sum('totalExpense'); 
					
					$totalExpense[] = $expenseSum;
				}
		
			}
			
		

			// $data = implode(', ', $totalRevenue);
			return $totalExpense;
		}


	function getLast30DaysList($day, $startDate , $endDate)
		{
			if(!empty($day))
            {
				$today     = new \DateTime();
				// $begin     = $today->sub(new \DateInterval('P0D'));
				
				if(($day == 1 ))
				{
					$begin = $today->sub(new \DateInterval('P0D'));
				}
				elseif (($day == 7)) 
				{
					$begin= $today->sub(new \DateInterval('P7D'));
				}
				elseif (($day == 30 )) 
				{
					$begin= $today->sub(new \DateInterval('P30D'));
				}
				$end       = new \DateTime();
				$end       = $end->modify('+1 day');
				$interval  = new \DateInterval('P1D');
				$daterange = new \DatePeriod($begin, $interval, $end);
				foreach ($daterange as $date) {
					$dateList[] = ''.$date->format("Y-m-d").'';
				}
            }

			if(!empty( $startDate))
            {
				$dateList = []; 
				$startDate = strtotime($startDate);
				$endDate = strtotime($endDate);
             
				for ($currentDate = $startDate; $currentDate <= $endDate; 
												$currentDate += (86400)) {
														
					$date = date('Y-m-d', $currentDate);
					$dateList[] = $date;
				}
		
			}
			
			return $dateList;
		}

		function getCategoryName($categoryDay)
		{
			// if(!empty($categoryDay))
            // {

			// 			$today     = new \DateTime();
			// 		// // $begin     = $today->sub(new \DateInterval('P30D'));

			// 		if(($categoryDay == 1 ))
			// 		{
			// 			$begin = $today->sub(new \DateInterval('P0D'));
			// 		}
			// 		elseif (($categoryDay == 7)) 
			// 		{
			// 			$begin= $today->sub(new \DateInterval('P7D'));
			// 		}
			// 		elseif (($categoryDay == 30 )) 
			// 		{
			// 			$begin= $today->sub(new \DateInterval('P30D'));
			// 		}

			// 		$end       = new \DateTime();
			// 		$end       = $end->modify('+1 day');
			// 		$interval  = new \DateInterval('P1D');
			// 		$daterange = new \DatePeriod($begin, $interval, $end);
					
			// 		// $tree = User::where('branch_id',$branch_id)->pluck('id')
			// 		// ->toArray();
			// 		$catogoryID =[];
			// 		foreach ($daterange as $date) {
						
			// 			// $categorySearch= OrderContain::whereDate('created_at',$date->format('Y-m-d'))->pluck('category_id');
			// 			$categorySearch= OrderContain::whereDate('created_at',$date->format('Y-m-d'))->get('category_id')->unique();
						
			// 			$catogoryID[] = $categorySearch;
			// 		}

			// 		// $catogoryName=[];
			// 		// foreach ($catogoryID as $category) {
						
			// 		// 	$catogoryName[]= Category::where('id',$category)->pluck('name')->first();
						
				
			// 		// }
					
			// }
			
 
			// $unique = $catogoryID->unique();
			 
			// $unique->values()->all();

			// $data = implode(', ', $totalRevenue);

			

			$categorySearch= DB::table("order_contains")->select('category_id')->whereDate('created_at','>', now()->subDays(30)->endOfDay())->unique()->all();

			// $users = DB::table("users")
			// 	->select('id')
			// 	->where('accounttype', 'standard')
			// 	->where('created_at', '>', now()->subDays(30)->endOfDay())
			// 	->all();	
			return $categorySearch;
		}

	function getDetails($startDate , $endDate, $category)
	{
		if(!empty($startDate))
		{
			$date = Carbon::createFromFormat('Y-m-d', $endDate);
$daysToAdd = 1;
$date = $date->addDays($daysToAdd);
			$a = 	DB::table('order_contains as w')
			->join("product_menus", "w.product_menu_id", "=", "product_menus.id")
			->where('w.category_id', $category)
			->whereBetween('w.created_at', [$startDate, date_format($date, "Y-m-d")])
			->select(array(DB::Raw('sum(w.quantity) as total_quantity'), DB::Raw('sum(w.netPrice) as total_netPrice'), DB::Raw('DATE(w.created_at) date'), 'w.product_menu_id', 'product_menus.name'))
			->groupBy(['date', 'w.product_menu_id', 'product_menus.name'])
            ->orderBy('w.created_at')
            ->get();
			$orderDetails = $a;
			return  $orderDetails;
	
		}

		
	}
	function getLast30TotalSales($day, $startDate , $endDate)
		{
			if(!empty($day))
            {

						$today     = new \DateTime();
					// // $begin     = $today->sub(new \DateInterval('P30D'));

					if(($day == 1 ))
					{
						$begin = $today->sub(new \DateInterval('P0D'));
					}
					elseif (($day == 7)) 
					{
						$begin= $today->sub(new \DateInterval('P7D'));
					}
					elseif (($day == 30 )) 
					{
						$begin= $today->sub(new \DateInterval('P30D'));
					}

					$end       = new \DateTime();
					$end       = $end->modify('+1 day');
					$interval  = new \DateInterval('P1D');
					$daterange = new \DatePeriod($begin, $interval, $end);
					
					$totalRevenue =[];
					foreach ($daterange as $date) {
						
						$salesSum = Order::whereDate('created_at',$date->format('Y-m-d'))->sum('netAmount'); 
						
						$totalSale[] = $salesSum;
					}

			}

			if(!empty( $startDate))
            {

				$rangArray = []; 
				$startDate = strtotime($startDate);
				$endDate = strtotime($endDate);
					
					for ($currentDate = $startDate; $currentDate <= $endDate; 
													$currentDate += (86400)) {
															
						$date = date('Y-m-d', $currentDate);
						$rangArray[] = $date;
					}
  			
				$totalRevenue =[];
				foreach ($rangArray as $date) {
					
					$salesSum = Order::whereDate('created_at',$date)->sum('netAmount'); 
					
					$totalSale[] = $salesSum;
				}
		
			}
			
		

			// $data = implode(', ', $totalRevenue);
			return $totalSale;
		}

	function getLast30details($day, $startDate , $endDate)
		{
			if(!empty($day))
            {
				$today     = new \DateTime();
				// $begin     = $today->sub(new \DateInterval('P30D'));
	
				if(($day == 1 ))
				{
					$begin = $today->sub(new \DateInterval('P0D'));
				}
				elseif (($day == 7)) 
				{
					$begin= $today->sub(new \DateInterval('P7D'));
				}
				elseif (($day == 30 )) 
				{
					$begin= $today->sub(new \DateInterval('P30D'));
				}
	
				$end       = new \DateTime();
				$end       = $end->modify('+1 day');
				$interval  = new \DateInterval('P1D');
				$daterange = new \DatePeriod($begin, $interval, $end);
				// foreach ($daterange as $date) {
				// 	$dateList[] = ''.$date->format("Y-m-d").'';
				// }
				// $orderDetails =[];
				foreach ($daterange as $date) {
					$orders['date']= $date->format("Y-m-d");
					$orders['sales']= OrderContain::whereDate('created_at',$date)->sum('netPrice');
					$orders['product'] = OrderContain::whereDate('created_at',$date)->sum('quantity'); 
					$orders['revenue']= OrderContain::whereDate('created_at',$date)->sum('netPrice');
					$orderDetails[] = $orders;
				}
				return $orderDetails;
				

			
			
		
			}

			if(!empty( $startDate))
            {

				$rangArray = []; 
				$startDate = strtotime($startDate);
				$endDate = strtotime($endDate);
					
					for ($currentDate = $startDate; $currentDate <= $endDate; 
													$currentDate += (86400)) {
															
						$date = date('Y-m-d', $currentDate);
						$rangArray[] = $date;
					}
  			
				// $orderDetails =[];
				foreach ($rangArray as $date) {
					$orders['date']= $date;
					$orders['sales']= OrderContain::whereDate('created_at',$date)->sum('netPrice');
					$orders['product'] = OrderContain::whereDate('created_at',$date)->sum('quantity'); 
					$orders['revenue']= OrderContain::whereDate('created_at',$date)->sum('netPrice');
					$orderDetails[] = $orders;
					
				}
				return $orderDetails;
			}
			else{
				foreach (OrderContain::select('created_at')->get() as $date ) {
					$onlydate = substr($date->created_at, 0,10);
					// echo $data;
					$data['date'] = $onlydate;
					$data['sales'] = OrderContain::whereDate('created_at', $onlydate)->sum('netPrice');
					$data['product'] = OrderContain::whereDate('created_at', $onlydate)->sum('quantity'); 
					$data['revenue'] = OrderContain::whereDate('created_at', $onlydate)->sum('netPrice');
					$details[] = $data;
					}
					return 	$details;
				}
			
			
			// $data = implode(', ', $totalProduct);
		}
	
