<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status',
        'email',
        'user_role',
        'user_attempt',
        'user_otp',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function getData($filters = [], $type = 'result', $searchableColumns = [], $orderColumn = '', $orderDirection = '', $perPage = 10, $offset = 0, $searchedFor = '')
    {
        // DB::connection()->enableQueryLog();

        $user_id = session('user_id');
        $queryItems = User::query()
        ->join('roles', 'users.user_role', '=', 'roles.id')
        ->select(
            'users.id as DT_RowId',
            'users.name',
            'users.status',
            'users.user_role',
            'users.email',
            'users.created_at',
            'roles.name as role_name'  
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
