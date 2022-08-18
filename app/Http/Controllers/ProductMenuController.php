<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductMenu;
use App\Models\Category;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductMenuController extends Controller
{
    public function searchProductMenu(Request $request)
    {
        try {
            // $query = DB::table('product_menus')
            $query = ProductMenu::join('categories', 'product_menus.subcategory_id', '=', 'categories.id')
                    ->select('product_menus.*', 'categories.name as subCategories_name')
                        // ->select('*')
                    ->whereNull('product_menus.parent_id')
                    ->with('halfPrice:parent_id,price,name,description,order_duration,category_id')
                    ->orderBy('product_menus.id', 'desc');

            if(!empty($request->id))
            {
                $query->where('product_menus.id', $request->id);
            }
            if(!empty($request->price))
            {
                $query->where('product_menus.price', $request->price);
            }
            if(!empty($request->category_id))
            {
                $query->where('product_menus.category_id', $request->category_id);
            }
            if(!empty($request->subcategory_id))
            {
                $query->where('product_menus.subcategory_id', $request->subcategory_id);
            }
            if(!empty($request->product))
            {
                $query->where('product', 'LIKE', '%'.$request->product.'%');
            }


            if(!empty($request->per_page_record))
            {
                $perPage = $request->per_page_record;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }

            return response(prepareResult(true, $query, trans('translate.fetched_records')), 200 , ['Result'=>'Your data has been saved successfully']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function store(Request $request)
    {
        // if(!empty($request->parent_id)){
        //     $productMenuData = ProductMenu::where('product_menus.id', $request->parent_id)->get('price')->first();
        // }
       

        $validation = Validator::make($request->all(), [
            // {($request->parent_id) ? $request->parent_id :null}
            
            // 'name'                    => ($request->parent_id) ? ' ': 'required',
            'description'                => ($request->parent_id) ? ' ': 'required',
            'category_id'                   => 'required|numeric',
            'subcategory_id'                => 'required|numeric',
            'category_id'                   => ($request->parent_id) ? ' ': 'required',
            'subcategory_id'                => ($request->parent_id) ? ' ': 'required',
            'price'                      => 'required|numeric',
    //    'price'                      => ($productMenuData->price <= $request->price) ? 'required|declined:false' : 'required',
           
          
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        } 

        if($request->parent_id){
            $productMenuData = ProductMenu::where('product_menus.id', $request->parent_id)->get('price')->first();

            $validation = Validator::make($request->all(),[     
               'price'  => ($productMenuData->price <= $request->price) ? 'required|declined:false' : 'required',   
               
            ],
            [
                'price.declined' => 'Half price is greater than full price'
            ]);
            if ($validation->fails()) {
                return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
            } 
        }

        DB::beginTransaction();
        try {
            $info = new ProductMenu;

            $category_name = Category::where('categories.id', $request->subcategory_id)->get('name')->first();

            // if(!empty($request->image))
            // {
            //   $file=$request->image;
            // $filename=time().'.'.$file->getClientOriginalExtension();
            // $info->image=$request->image->move('assets',$filename);
            // }

            
            if(!empty($request->image))
            {
              $file=$request->image;
            $filename=time().'.'.$file->getClientOriginalExtension();
            $info->image=imageBaseURL().$request->image->move('assets',$filename);
            }

            // $file=$request->image;
            // $filename= $file ? time().'.'.$file->getClientOriginalExtension() : "";

            // $info->image=$request->file->move('assets',$filename);
            $info->name = ($request->name) ? $request->name : $category_name->name;
            $info->order_duration = $request->order_duration;
            $info->description = $request->description;
            // $info->image_url = $request->image_url;
            $info->category_id = $request->category_id;
            $info->subcategory_id = $request->subcategory_id;
            $info->price = $request->price;
            $info->parent_id = ($request->parent_id) ? $request->parent_id :null;
            $info->is_parent = $request->is_parent;
            $info->save();

            DB::commit();
            // $info['product_menus'] = $info->halfPrice;
            return response()->json(prepareResult(true, $info, trans('translate.created')), 200 , ['Result'=>'Your data has been saved successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            //  'name'                    => 'required',
             'description'                => 'required',
             'category_id'                   => 'nullable|numeric',
             'subcategory_id'                => 'nullable|numeric',
             // 'price'                      => 'required|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {
            
            $info = ProductMenu::find($id);

            // if(!empty($request->image))
            // {
            //   $file=$request->image;
            // $filename=time().'.'.$file->getClientOriginalExtension();
            // $info->image=imageBaseURL().$request->image->move('assets',$filename);
            // }
            
            if(!empty($request->image))
            {
                if(gettype($request->image) == "string"){
                    $info->image = $request->image;
                }
                else{
                       $file=$request->image;
                        $filename=time().'.'.$file->getClientOriginalExtension();
                        $info->image=imageBaseURL().$request->image->move('assets',$filename);
                }

            //   $file=$request->image;
            // $filename=time().'.'.$file->getClientOriginalExtension();
            // $info->image=imageBaseURL().$request->image->move('assets',$filename);
            }

            $info->name = $request->name;
            $info->order_duration = $request->order_duration;
            $info->description = $request->description;
            // $info->image_url = $request->image_url;
            $info->category_id = $request->category_id;
            $info->subcategory_id = $request->subcategory_id;
            $info->price = $request->price;
            $info->parent_id = ($request->parent_id) ? $request->parent_id :null;
            $info->is_parent = $request->is_parent;
            $info->save();
            DB::commit();
            return response()->json(prepareResult(true, $info, trans('translate.created')), 200 , ['Result'=>'Your data has been saved successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function show($id)
    {
        try {
            
            $info = ProductMenu::find($id);
            if($info)
            {
                // return response(prepareResult(false, $info, trans('translate.fetched_records')), config('httpcodes.success'));
                return response(prepareResult(true, $info, trans('translate.fetched_records')), 200 , ['Result'=>'httpcodes.found']);
            }
            return response(prepareResult(false, [], trans('translate.record_not_found')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }

    public function destroy($id)
    {
        try {
            
            $info = ProductMenu::find($id);
            if($info)
            {
                $result=$info->delete();
                return response(prepareResult(true, $result, trans('sucess')), 200 , ['Result'=>'httpcodes.found']);
            }
            return response(prepareResult(false, [], trans('translate.record_not_found')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }

}
