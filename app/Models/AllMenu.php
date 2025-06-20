<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class AllMenu extends Model
{
    use Searchable;


    protected $table = 'all_menu';

    protected $primaryKey = 'id';

    // Optional: define searchable fields
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,    
            'title' => $this->title,
            'link' => $this->link,
        ];
    }
}
