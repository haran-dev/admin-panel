<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Http\Request\CategoryRequest;
use Modules\Admin\Http\Request\RolesRequest;
use Modules\Admin\Http\Request\UsermanagementRequest;
use Modules\Admin\Models\Roles;
use app\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('admin::user-management.list');
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
                    'status' => 1,
                    'email' => $postArray->email,
                    'user_role' => $postArray->role,
                    'user_attempt' => 3,
                    'user_otp' => $otp,
                    'email_verified_at' => null,
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
                'target' => '#modal-view',
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
        $data['title'] = 'Edit User';
        $user = User::find($request->id);
        $data['details'] = $user;
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


    public function update(UsermanagementRequest $request)
    {
        DB::beginTransaction();

        try {
            $postArray = (object) $request->all();
            $actionId = $postArray->action_id;
            $user_id = session('user_id'); // Optional tracking

            if ($postArray && $actionId) {
                $user = User::findOrFail($actionId); // ✅ Find existing user

                $otp = rand(100000, 999999);

                // ✅ Update only what's changed
                $user->name = $postArray->username;
                $user->email = $postArray->email;
                $user->user_role = $postArray->role;
                $user->status = 1;
                $user->user_attempt = 3;
                $user->user_otp = $otp;
                $user->email_verified_at = null;
                $user->updated_at = now();

                // ✅ Only update password if filled
                if (!empty($postArray->password)) {
                    $user->password = Hash::make($postArray->password);
                }

                $user->save();

                // ✅ Update role
                $role = Roles::findOrFail($postArray->role);
                $user->syncRoles([$role->name]); // Better than assignRole() for updates

                DB::commit();

                return response()->json([
                    'target' => '#modal-view',
                    'status' => 'success',
                    'message' => 'User updated successfully.',
                    'redirect_url' => url('/user/view'),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data. Please try again.',
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



    public function userStatusUpdate(Request $request)
    {
        try {
            $user = User::find($request->id);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not find',
                ]);
            }

            $user->status = $request->status;
            $user->save();

            return response()->json([
                'target' => '#modal-view',
                'status' => 'success',
                'message' => 'User status updated.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating user status.' . $e->getMessage(),
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



    public function getRoleById($id, Request $request)
    {
        if ($id) {
            $role = Roles::select('id', 'name')->find($id);
            return response()->json($role);
        }

        $term = $request->get('term');
        return Roles::select('id', 'name')
            ->when($term, fn($q) => $q->where('name', 'like', "%{$term}%"))
            ->get();
    }
}
