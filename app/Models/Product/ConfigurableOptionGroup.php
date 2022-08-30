<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurableOptionGroup extends Model
{
    use HasFactory;

    protected $table = 'product_configurable_option_groups';

    protected $fillable = [
        'name',
        'description',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function configurableOption()
    {
        return $this->hasMany(ConfigurableOption::class, 'group_id');
    }
}
