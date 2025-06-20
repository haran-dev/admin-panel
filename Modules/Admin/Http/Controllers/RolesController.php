<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Admin\Http\Request\CategoryRequest;
use Modules\Admin\Http\Request\RolesRequest;
use Modules\Admin\Models\Roles;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public function index()
    {
        return view('admin::roles.list');
    }


    public function create()
    {
        $data['crudAction'] = 'add';
        $data['title'] = 'Add Roles';
        $data['details'] = (object) [];
        $permissions = Permission::get();
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            [$action, $label] = explode(' ', $permission->name); 
            $groupedPermissions[$label][] = [
                'id' => $permission->id,
                'action' => $action,
                'full_name' => $permission->name,
            ];
        }
        $data['groupedPermissions'] = $groupedPermissions;
        $view = view('admin::roles.crud', $data)->render();
        $returnArray = array(
            'status' => 'model',
            'data' => array('view' => $view, 'target' => '#modal-view'),
            'message' => '[]',
            'redirect' => '[]',
            'renderType' => 'modal'
        );

        return response()->json($returnArray);
    }


    public function store(RolesRequest $request)
    {
        DB::beginTransaction();
        try {
            $postArray = (object) $request->all();
            $user_id = session('user_id');
            if ($postArray) {
                $role = Roles::create([
                    'name' => $postArray->roles_name,
                    'status' => 1,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $permissions = Permission::whereIn('id', $postArray->permissions)->pluck('name')->toArray();
                $role->syncPermissions($permissions);

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
                'message' => 'Roles added successfully.',
                'redirect_url' => url('/roles/view'),
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
        $model = new Roles();
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
        $data['title'] = 'Edit Roles';
        $role = Roles::find($request->id);
        $data['details'] = $role;
        $checkedPermissions = DB::table('role_has_permissions')
        ->where('role_id', $request->id)
        ->pluck('permission_id') 
        ->toArray();
        $data['checkedPermissions'] = $checkedPermissions;

        $permissions = Permission::get();
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            [$action, $label] = explode(' ', $permission->name); 
            $groupedPermissions[$label][] = [
                'id' => $permission->id,
                'action' => $action,
                'full_name' => $permission->name,
            ];
        }
        $data['groupedPermissions'] = $groupedPermissions;



        $view = view('admin::roles.crud', $data)->render();
        $returnArray = array(
            'status' => 'model',
            'data' => array('view' => $view, 'target' => '#modal-view'),
            'message' => '[]',
            'redirect' => '[]',
            'renderType' => 'modal'
        );
        return response()->json($returnArray);
    }


    public function update(RolesRequest $request) 
    {
        DB::beginTransaction();
        try {
            $postArray = (object) $request->all();
            $actionId =  $postArray->action_id;
            $user_id = session('user_id');


            if (!property_exists($request, 'permissions') || empty($request->permissions)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please select at least one permission.',
                ]);
            }



            if ($postArray && $actionId ) {
                $role = Roles::where('id', $actionId)->first();
                $role->update([
                    'name' => $postArray->roles_name,
                    'status' => 1,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $permissions = Permission::whereIn('id', $postArray->permissions)->pluck('name')->toArray();
                $role->syncPermissions($permissions);

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
                'message' => 'Role updated successfully.',
                'redirect_url' => url('/roles/view'),
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


    public function rolesStatusUpdate(Request $request)
    {
        try {
            $roles = Roles::find($request->id);

            if (!$roles) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not find',
                ]);
            }

            $roles->status = $request->status;
            $roles->save();

            return response()->json([
                'target' => '#modal-view',
                'status' => 'success',
                'message' => 'Role status updated.',
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
            $role = Roles::findOrFail($id);

            // Check if any user is assigned to this role
            if (User::where('user_role', $id)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This role is assigned to one or more users and cannot be deleted.',
                ]);
            }

            // Prevent deletion of Admin role
            if (trim(strtolower($role->name)) === 'admin') {
                return response()->json([
                    'target' => '#modal-view',
                    'status' => 'error',
                    'message' => 'The Admin role cannot be deleted.',
                ]);
            }

            $role->delete();

            return response()->json([
                'target' => '#modal-view',
                'status' => 'success',
                'message' => 'Role deleted successfully.',
                'redirect_url' => url('/categories/view'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage(), // You can remove this in production for security
            ]);
        }
    }





}
