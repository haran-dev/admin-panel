<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Http\Request\CategoryRequest;
use Modules\Admin\Http\Request\RolesRequest;
use Modules\Admin\Models\Roles;
use Modules\Admin\Models\SmsMarketing;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Modules\Admin\Models\SmsApiSetting;
use Illuminate\Support\Facades\Http;


class SmsController extends Controller
{
    public function index()
    {
        return view('admin::marketing.sms.list');
    }


    public function create()
    {
        $data['crudAction'] = 'add';
        $data['title'] = 'Add Data';
        $data['details'] = (object) [];

        $view = view('admin::marketing.sms.crud', $data)->render();
        $returnArray = array(
            'status' => 'model',
            'data' => array('view' => $view, 'target' => '#modal-view'),
            'message' => '[]',
            'redirect' => '[]',
            'renderType' => 'modal'
        );

        return response()->json($returnArray);
    }


    public function store(Request $request)
    {
        if (!$request->hasFile('csv_file')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please Upload your File',
            ]);
        }

        $file = $request->file('csv_file');

        if (!$file->isValid() || !in_array($file->getClientOriginalExtension(), ['csv', 'txt'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid file format. Please upload a valid CSV file.',
            ]);
        }


        $filePath = $file->getRealPath();
        if (filesize($filePath) === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'The uploaded CSV file is empty.',
            ]);
        }

        $csvData = array_map('str_getcsv', file($filePath));

        if (count($csvData) < 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'CSV file must have at least one data row.',
            ]);
        }

        $header = $csvData[0];
        if (count($header) < 2 || strtolower($header[0]) !== 'name' || strtolower($header[1]) !== 'number') {
            return response()->json([
                'status' => 'error',
                'message' => 'CSV headers must be: name, number.',
            ]);
        }

        $user_id = session('user_id');

        DB::beginTransaction();
        try {
            $skipped = [];
            $added = [];
            $existing = [];

            $user_id = session('user_id');

            for ($i = 1; $i < count($csvData); $i++) {
                $row = $csvData[$i];

                if (count($row) < 2) {
                    $skipped[] = ['row' => $row, 'reason' => 'Missing columns'];
                    continue;
                }

                $name = trim($row[0]);
                $number = trim($row[1]);

                // Add leading 0 if number is 9 digits
                if (preg_match('/^\d{9}$/', $number)) {
                    $number = '0' . $number;
                }

                // Validate number: must be exactly 10 digits
                if (!preg_match('/^\d{10}$/', $number)) {
                    $skipped[] = ['name' => $name, 'number' => $number, 'reason' => 'Invalid mobile number'];
                    continue;
                }

                // Check for duplicate in uploaded file
                $duplicateKey = $name . '-' . $number;
                if (in_array($duplicateKey, $existing)) {
                    $skipped[] = ['name' => $name, 'number' => $number, 'reason' => 'Duplicate entry in file'];
                    continue;
                }
                $existing[] = $duplicateKey;

                // Check if already exists in DB
                $alreadyExists = SmsMarketing::where('name', $name)
                    ->where('mobile_number', $number)
                    ->exists();

                if ($alreadyExists) {
                    $skipped[] = ['name' => $name, 'number' => $number, 'reason' => 'Already exists in database'];
                    continue;
                }

                // Save to DB
                SmsMarketing::create([
                    'name' => $name,
                    'mobile_number' => $number,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                ]);
                $added[] = ['name' => $name, 'number' => $number];
            }

            DB::commit();

            return response()->json([
                'status' => 'sms-success',
                'message' => count($added) . ' numbers added successfully.',
                'skipped' => $skipped,
                'added' => $added,
                'redirect_url' => url('/sms/view'),
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


    public function manualStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $skipped = [];
            $added = [];
            $existing = [];

            $user_id = session('user_id');

            $names = $request->input('names', []);
            $mobile_numbers = $request->input('mobile_numbers', []);

            for ($i = 0; $i < count($names); $i++) {
                $name = trim($names[$i]);
                $number = trim($mobile_numbers[$i]);

                if (empty($name) || empty($number)) {
                    continue;
                }

                // Add leading 0 if number is 9 digits
                if (preg_match('/^\d{9}$/', $number)) {
                    $number = '0' . $number;
                }

                // Validate number: must be exactly 10 digits
                if (!preg_match('/^\d{10}$/', $number)) {
                    $skipped[] = ['name' => $name, 'number' => $number, 'reason' => 'Invalid mobile number'];
                    continue;
                }

                // Check for duplicate in this submission
                $duplicateKey = $name . '-' . $number;
                if (in_array($duplicateKey, $existing)) {
                    $skipped[] = ['name' => $name, 'number' => $number, 'reason' => 'Duplicate in form'];
                    continue;
                }
                $existing[] = $duplicateKey;

                // Check if already exists in DB
                $alreadyExists = SmsMarketing::where('name', $name)
                    ->where('mobile_number', $number)
                    ->exists();

                if ($alreadyExists) {
                    $skipped[] = ['name' => $name, 'number' => $number, 'reason' => 'Already exists in database'];
                    continue;
                }

                // Save to DB
                SmsMarketing::create([
                    'name' => $name,
                    'mobile_number' => $number,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                ]);
                $added[] = ['name' => $name, 'number' => $number];
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => count($added) . ' numbers added successfully.',
                'skipped' => $skipped,
                'added' => $added,
                'redirect_url' => url('/sms/view'),
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

    public function sendSms(Request $request)
    {
        try {
            $ids = $request->input('ids');

            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No valid IDs received.',
                ], 400);
            }

            $defaultMessage = "This is a test SMS.";  // Your fixed message

            $apiSetting = SmsApiSetting::latest()->first(); // get most recent API config

            if (!$apiSetting || !$apiSetting->api_key || !$apiSetting->user_code || !$apiSetting->sender_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'SMS API credentials are missing.',
                ], 400);
            }

            $certPath = storage_path('certs/cacert.pem');

            foreach ($ids as $id) {
                $record = SmsMarketing::find($id);
                if ($record) {
                    $mobile = $record->mobile_number;

                    // Normalize phone number
                    if (str_starts_with($mobile, '0')) {
                        $mobile = '94' . substr($mobile, 1);
                    }

                    // Send SMS using Notify.lk API with SSL cert verification
                    $response = Http::withOptions([
                        'verify' => $certPath,
                    ])->asForm()->post('https://app.notify.lk/api/v1/send', [
                        'user_id' => $apiSetting->user_code,
                        'api_key' => $apiSetting->api_key,
                        'sender_id' => $apiSetting->sender_id,
                        'to' => $mobile,
                        'message' => $defaultMessage,
                    ]);

                    // You can check $response here if needed
                }
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Messages sent successfully.',
                'redirect_url' => url('/sms/view'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function fetchList(Request $request)
    {
        $model = new SmsMarketing();
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


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20'
        ]);

        $sms = SmsMarketing::findOrFail($id);
        $sms->name = $request->name;
        $sms->mobile_number = $request->mobile_number;
        $sms->save();

        return response()->json(['status' => 'success', 'message' => 'Data updated successfully.']);
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
}
