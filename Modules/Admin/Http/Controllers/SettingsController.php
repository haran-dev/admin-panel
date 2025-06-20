<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Http\Request\CategoryRequest;
use Modules\Admin\Http\Request\RolesRequest;
use Modules\Admin\Http\Request\UsermanagementRequest;
use Modules\Admin\Http\Request\notifyApiRequest;
use Modules\Admin\Models\Roles;
use Modules\Admin\Models\SmsApiSetting;
use app\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function index()
    {
        $smsApiSetting = SmsApiSetting::first();

        $certPath = storage_path('certs/cacert.pem');
        $balance = null;
        $activeStatus = null;
        $subscriptionStatus = null;

        $response = Http::withOptions([
            'verify' => $certPath,
        ])->get('https://app.notify.lk/api/v1/status', [
            'user_id' => $smsApiSetting->user_code,
            'api_key' => $smsApiSetting->api_key,
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'] ?? null;

            if ($data) {
                $balance = $data['acc_balance'] ?? null;
                $activeStatus = $data['active'] ?? null;
                $subscriptionStatus = $data['subscription'] ?? null;
            }
        }





        return view('admin::settings.list', compact('smsApiSetting', 'balance', 'activeStatus', 'subscriptionStatus'));
    }


    public function notifyApiStore(notifyApiRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->only(['api_key', 'user_code', 'sender_id']);
            $actionId = $request->input('action_id');

            if ($actionId) {
                $apiSetting = SmsApiSetting::find($actionId);

                if (!$apiSetting) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Record not found.',
                    ]);
                }

                $apiSetting->update($data);
            } else {
                SmsApiSetting::create($data);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $actionId ? 'API keys updated successfully.' : 'API keys added successfully.',
                'redirect_url' => url('/settings/view'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }



    public function create()
    {
        $data['crudAction'] = 'add';
        $data['title'] = 'Add User';
        $data['details'] = (object) [];
        $data['roles'] = Roles::where('status', 1)->get();
        $view = view('admin::user-management.crud', $data)->render();
        $returnArray = array(
            'status' => 'model',
            'data' => array('view' => $view, 'target' => '#modal-view'),
            'message' => '[]',
            'redirect' => '[]',
            'renderType' => 'modal'
        );

        return response()->json($returnArray);
    }


    public function store(UsermanagementRequest $request)
    {
        DB::beginTransaction();
        try {
            $postArray = (object) $request->all();
            $user_id = session('user_id');

            $otp = rand(100000, 999999);
            if ($postArray) {
                $usermanagement = [
                    'name' => $postArray->username,
                    'status' => 0,
                    'email' => $postArray->email,
                    'user_role' => $postArray->role,
                    'user_attempt' => 3,
                    'user_otp' => $otp,
                    'email_verified_at' => now(),
                    'password' => Hash::make($postArray->password),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $user = User::create($usermanagement);
                $role = Roles::findOrFail($postArray->role);
                $user->assignRole($role->name);



            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong. Please try again.',
                ]);
            }



            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User added successfully.',
                'redirect_url' => url('/user-management/view'),
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
        $model = new User();
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
        $category = Roles::find($request->id);
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

                $categories = Roles::where('id', $actionId)->first();

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
            $category = Roles::find($request->id);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not find',
                ]);
            }

            $category->status = $request->status;
            $category->save();

            return response()->json([
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
            $category = Roles::findOrFail($id);
            $category->delete();
            return response()->json([
                'status' => 'delete',
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
    

    public function select2(Request $request)
    {
        $search = $request->input('term');

        $roles = Roles::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->where('status', 1)
            ->get(['id', 'name']);

        return response()->json($roles);
    }


}
