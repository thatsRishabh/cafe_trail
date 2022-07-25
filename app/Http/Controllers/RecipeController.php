<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\RecipeContains;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductInfo;

class RecipeController extends Controller
{

    public function searchRecipe(Request $request)
    {
        try {
            $query = Recipe::select('*')
            ->join('product_menus', 'recipes.product_menu_id', '=', 'product_menus.id')
            ->select('recipes.*','product_menus.name as product_menu_name' )
                    ->with('recipeMethods:recipe_id,name,quantity,unit_id')
                    ->orderBy('id', 'desc');

        //   $query = RecipeContains::select('*')
        //         ->with('recipes')
        //         ->orderBy('id', 'desc');
        //        in above we can retrive parrent data via child

            if(!empty($request->id))
            {
                $query->where('id', $request->id);
            }
            if(!empty($request->title))
            {
                $query->where('title', $request->title);
            }
            if(!empty($request->recipe_status))
            {
                $query->where('recipe_status', $request->recipe_status);
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
        $validation = Validator::make($request->all(), [
            // 'name'                    => 'required',
            'recipe_status'                    => 'required|numeric',
            'description'                => 'required',
            // 'name'                      => 'required',
            // 'quantity'                   => 'nullable|numeric',
            // 'unit_id'                => 'nullable|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {
            $info = new Recipe;
            $info->name = $request->name;
            $info->product_menu_id = $request->product_menu_id;
            $info->description = $request->description;
            $info->recipe_status = $request->recipe_status;
            $info->save();
           
           foreach ($request->recipe_methods as $key => $recipe) {
               $addRecipe = new RecipeContains;
               $addRecipe->recipe_id =  $info->id;
               $addRecipe->name = $recipe['name'];
               $addRecipe->product_info_stock_id = $recipe['product_info_stock_id'];
               $addRecipe->quantity = $recipe['quantity'];
               $addRecipe->unit_id = $recipe['unit_id'];
               $addRecipe->save();
               
             // getting old stock value
            $oldValue = ProductInfo::where('product_infos.id', $recipe['product_info_stock_id'])->get('current_quanitity')->first();
             // updating the productinfo table as well
             $updateStock = ProductInfo::find($recipe['product_info_stock_id']);
             $updateStock->current_quanitity =  $oldValue->current_quanitity - unitConversion($recipe['unit_id'], $recipe['quantity']);
             $updateStock->save();

           }

            DB::commit();
            $info['recipe_contains'] = $info->recipeMethods;
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
            // 'name'                    => 'required',
            'description'                => 'required',
             'recipe_status'                    => 'required|numeric',
            // 'name'                      => 'required',
            // 'quantity'                   => 'nullable|numeric',
            // 'unit_id'                => 'nullable|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {

            $info = Recipe::find($id);
            $info->name = $request->name;
            $info->product_menu_id = $request->product_menu_id;
            $info->description = $request->description;
            $info->recipe_status = $request->recipe_status;
            $info->save();
           
            
        $deletOld = RecipeContains::where('recipe_id', $id)->delete();
           foreach ($request->recipe_methods as $key => $recipe) {
            $product_info_name = ProductInfo::where('product_infos.id', $recipe['product_info_stock_id'])->get('name')->first();

            // $addRecipe=RecipeContains::find($recipe['id']);
               $addRecipe= new RecipeContains;
                $addRecipe->recipe_id = $id;
               $addRecipe->name = $product_info_name->name;
               $addRecipe->product_info_stock_id = $recipe['product_info_stock_id'];
               $addRecipe->quantity = $recipe['quantity'];
               $addRecipe->unit_id = $recipe['unit_id'];
               $addRecipe->save();
               
                // getting old stock value
            $oldValue = ProductInfo::where('product_infos.id', $recipe['product_info_stock_id'])->get('current_quanitity')->first();
            // updating the productinfo table as well
            $updateStock = ProductInfo::find($recipe['product_info_stock_id']);
            $updateStock->current_quanitity =  $oldValue->current_quanitity - unitConversion($recipe['unit_id'], $recipe['quantity']);
            $updateStock->save();
           
           }

            DB::commit();
            $info['recipe_contains'] = $info->recipeMethods;
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
            
            $info = Recipe::with('recipeMethods')->find($id);
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
            
            $info = Recipe::find($id);
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
