<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $menus = DB::table('all_menu')
            ->whereRaw("SOUNDEX(title) = SOUNDEX(?)", [$query]) // fuzzy match
            ->orWhere('title', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($menus);
    }
}
