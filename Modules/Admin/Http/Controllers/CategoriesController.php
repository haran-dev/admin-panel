<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Http\Request\CategoryRequest;
use Modules\Admin\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function index()
    {
        return view('admin::categories.list');
    }


    public function create()
    {
        $data['crudAction'] = 'add';
        $data['title'] = 'Add Categories';
        $data['details'] = (object) [];
        $view = view('admin::categories.crud', $data)->render();
        $returnArray = array(
            'status' => 'model',
            'data' => array('view' => $view, 'target' => '#modal-view'),
            'message' => '[]',
            'redirect' => '[]',
            'renderType' => 'modal'
        );

        return response()->json($returnArray);
    }


    public function store(CategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $postArray = (object) $request->all();
            $user_id = session('user_id');


            if ($postArray) {


                $categories = [
                    'category_name' => $postArray->category_name,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                Category::create($categories);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong. Please try again.',
                ]);
            }



            DB::commit();
            return response()->json([
                'target' => '#modal-view',
                'status' => 'success',
                'message' => 'Category added successfully.',
                'redirect_url' => url('/categories/view'),
            ]);
        } catch (\Exception $e) {
            // Rollback in case of an error
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function fetchList(Request $request)
    {
        $model = new Category();
        $data = $request->all();
        $dataTableDraw = $data['draw']; // unique identifier of the request. after initail page load,value increase by one for every datatable request
        $dataTableColumns = $data['columns']; // all columns with information
        $dataTableOrder = $data['order'][0]; // ordering information with default/selected column index and direction.
        $dataTableStart = $data['start']; // starting index for records to fetch
        $dataTableLength = $data['length']; // number of records to display per page
        $dataTableSearchValue = $data['search']['value']; // global search value entered in the search box.
        $orderColumnName = $dataTableColumns[$dataTableOrder['column']]['data']; // extracted column name for ordering.
        $searchableColumns = [];
        $filters = [];
        if (!empty($dataTableColumns)) {
            for ($i = 0; $i < count($dataTableColumns); $i++) {
                if ($dataTableColumns[$i]['search']['value'] != '') {
                    $filters[$dataTableColumns[$i]['name']] = $dataTableColumns[$i]['search']['value'];
                }
                if (!empty($dataTableSearchValue) && $dataTableColumns[$i]['searchable'] == "true") {
                    // Add column data/name to the seachable array
                    $searchableColumns[] = $dataTableColumns[$i]['data'];
                }
            }
        }
        [$resultData, $resultDataCount] = $model->getData($filters, 'result', $searchableColumns, $orderColumnName, $dataTableOrder['dir'], $dataTableLength, $dataTableStart == 1 ? 0 : $dataTableStart, $dataTableSearchValue);
        $totalCount = $model->count();


        /* dd($data); */
        $dTblArray = array(
            "draw" => $dataTableDraw,
            "recordsTotal" => $totalCount,
            "recordsFiltered" => $resultDataCount,
            'data' => (array)$resultData,
        );


        return response()->json($dTblArray);
    }


    public function edit(Request $request)
    {
        $data['crudAction'] = 'edit';
        $data['title'] = 'Edit Categories';
        $category = Category::find($request->id);
        $data['details'] = $category;
        $view = view('admin::categories.crud', $data)->render();
        $returnArray = array(
            'status' => 'model',
            'data' => array('view' => $view, 'target' => '#modal-view'),
            'message' => '[]',
            'redirect' => '[]',
            'renderType' => 'modal'
        );
        return response()->json($returnArray);
    }


    public function update(CategoryRequest $request) 
    {
        DB::beginTransaction();
        try {
            $postArray = (object) $request->all();
            $actionId =  $postArray->action_id;
            $user_id = session('user_id');


            if ($postArray && $actionId ) {

                $categories = Category::where('id', $actionId)->first();

                $categories->update([
                    'category_name' => $postArray->category_name,
                    'updated_by' => $user_id,
                    'updated_at' => now(),
                ]);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong. Please try again.',
                ]);
            }



            DB::commit();
            return response()->json([
                'target' => '#modal-view',
                'status' => 'success',
                'message' => 'Category updated successfully.',
                'redirect_url' => url('/categories/view'),
            ]);
        } catch (\Exception $e) {
            // Rollback in case of an error
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function categoriesStatusUpdate(Request $request)
    {
        try {
            $category = Category::find($request->id);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not find',
                ]);
            }

            $category->status = $request->status;
            $category->save();

            return response()->json([
                'target' => '#modal-view',
                'status' => 'success',
                'message' => 'Category status updated.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating category status.' . $e->getMessage(),
            ]);
        }
    }


    public function delete(Request $request)
    {
        try {
            $id = $request->id;
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json([
                'target' => '#modal-view',
                'status' => 'error',
                'message' => 'Category deleted successfully.',
                'redirect_url' => url('/categories/view'),
            ]);
        } catch (\Exception $e) {
             return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }



}
