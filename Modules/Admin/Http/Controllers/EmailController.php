<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Http\Request\CategoryRequest;
use Modules\Admin\Http\Request\RolesRequest;
use Modules\Admin\Models\Roles;
use Modules\Admin\Models\SmsMarketing;
use Modules\Admin\Models\EmailMarketting;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Modules\Admin\Models\SmsApiSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\emailSending;

class EmailController extends Controller
{
    public function index()
    {
        return view('admin::marketing.email.list');
    }


    public function create()
    {
        $data['crudAction'] = 'add';
        $data['title'] = 'Add Data';
        $data['details'] = (object) [];

        $view = view('admin::marketing.email.crud', $data)->render();
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
        if (count($header) < 2 || strtolower($header[0]) !== 'name' || strtolower($header[1]) !== 'email') {
            return response()->json([
                'status' => 'error',
                'message' => 'CSV headers must be: name, Email.',
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
                $email = trim($row[1]);

                

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped[] = ['name' => $name, 'email' => $email, 'reason' => 'Invalid email address'];
                    continue;
                }


                // Check for duplicate in uploaded file
                $duplicateKey = $name . '-' . $email;
                if (in_array($duplicateKey, $existing)) {
                    $skipped[] = ['name' => $name, 'email' => $email, 'reason' => 'Duplicate entry in file'];
                    continue;
                }
                $existing[] = $duplicateKey;

                // Check if already exists in DB
                $alreadyExists = EmailMarketting::where('name', $name)
                    ->where('email', $email)
                    ->exists();

                if ($alreadyExists) {
                    $skipped[] = ['name' => $name, 'email' => $email, 'reason' => 'Already exists in database'];
                    continue;
                }

                // Save to DB
                EmailMarketting::create([
                    'name' => $name,
                    'email' => $email,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                ]);
                $added[] = ['name' => $name, 'email' => $email];
            }

            DB::commit();

            return response()->json([
                'status' => 'email-success',
                'message' => count($added) . ' Email added successfully.',
                'skipped' => $skipped,
                'added' => $added,
                'redirect_url' => url('/email/view'),
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
            $emails  = $request->input('email', []);



            for ($i = 0; $i < count($names); $i++) {
                $name = trim($names[$i]);
                $email = trim($emails[$i]);

                if (empty($name) || empty($email)) {
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped[] = ['name' => $name, 'email' => $email, 'reason' => 'Invalid email address'];
                    continue;
                }

                // Check for duplicate in this submission
                $duplicateKey = $name . '-' . $email;
                if (in_array($duplicateKey, $existing)) {
                    $skipped[] = ['name' => $name, 'email' => $email, 'reason' => 'Duplicate in form'];
                    continue;
                }
                $existing[] = $duplicateKey;

                // Check if already exists in DB
                $alreadyExists = EmailMarketting::where('name', $name)
                    ->where('email', $email)
                    ->exists();

                if ($alreadyExists) {
                    $skipped[] = ['name' => $name, 'email' => $email, 'reason' => 'Already exists in database'];
                    continue;
                }

                // Save to DB
                EmailMarketting::create([
                    'name' => $name,
                    'email' => $email,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                ]);
                $added[] = ['name' => $name, 'email' => $email];
            }

            DB::commit();

            return response()->json([
                'status' => 'email-success',
                'message' => count($added) . ' Email added successfully.',
                'skipped' => $skipped,
                'added' => $added,
                'redirect_url' => url('/email/view'),
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

    public function sendEmail(Request $request)
    {
        try {
            $ids = $request->input('ids');

            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No valid IDs received.',
                ], 400);
            }

            

            foreach ($ids as $id) {
                $record = EmailMarketting::find($id);

                if ($record) {
                    $name = $record->name;
                    $email = $record->email;

                    $subject = '🎉 You’ve Earned a Reward!';
                    $body = "Hi $name,\n\nThanks for being a loyal customer. As a token of our appreciation, we're giving you **Rs.500 OFF** your next order.\n\nUse code: **LOYAL500** at checkout.\n\nHurry – this offer is valid for a limited time only!\n\nThank you,\nTeam";

                    Mail::to($email)->send(new EmailSending($subject, $body, $name));
                }
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Messages sent successfully.',
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
        $model = new EmailMarketting();
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
            'email' => 'required|string|max:255'
        ]);



        $emails = EmailMarketting::findOrFail($id);
        $emails->name = $request->name;
        $emails->email = $request->email;
        $emails->save();

        return response()->json(['status' => 'success', 'message' => 'Data updated successfully.']);
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
