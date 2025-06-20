<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;

class Roles extends SpatieRole
{
  

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'status',
        'guard_name',
        'created_at',
        'updated_at',
    ];


    public function getData($filters = [], $type = 'result', $searchableColumns = [], $orderColumn = '', $orderDirection = '', $perPage = 10, $offset = 0, $searchedFor = '')
    {
        // DB::connection()->enableQueryLog();

        $user_id = session('user_id');
        $queryItems = roles::query()
        ->select(
            'id as DT_RowId',
            'name',
            'status'
        );



        foreach ($filters as $column => $value) {
            $queryItems->where($column, $value);
        }

        // Dynamic search with searchable columns
        if (!empty($searchedFor) && !empty($searchableColumns)) {
            $queryItems->where(function ($query) use ($searchedFor, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', "%$searchedFor%");
                }
            });
        }

        // Apply ordering if specified
        if ($type == 'result' && $orderColumn != '' && $orderDirection != '') {
            $queryItems->orderBy($orderColumn, $orderDirection);
        } else {
            $queryItems->orderBy('roles.id', 'desc'); // Specify user_bank.id explicitly
        }

        // Get the total count before applying pagination
        $resultDataCount = $queryItems->count();

        // Apply pagination if specified
        if ($perPage != '') {
            $queryItems->offset($offset)->limit($perPage);
        }

        // Execute the query and get the result
        $result = $queryItems->get()->toArray();

        if ($type == 'result') {
            return [$result, $resultDataCount];
        }

        if ($type == 'count') {
            return $resultDataCount;
        }
    }
}
