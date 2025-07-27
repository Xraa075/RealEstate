<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LandAsset extends Model
{
    use HasFactory;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'asset_code',
        'name',
        'description',
        'area_sqm',
        'address',
        'status',
        'value',
        'geometry',
        'owner_name',
        'owner_contact',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'area_sqm' => 'decimal:2',
        'value' => 'decimal:2',
        'geometry' => 'json',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the documents for the land asset.
     */
    public function documents()
    {
        return $this->hasMany(AssetDocument::class, 'asset_id');
    }

    /**
     * Get the requests for the land asset.
     */
    public function requests()
    {
        return $this->hasMany(AssetRequest::class, 'asset_id');
    }

    /**
     * Get the user who created the land asset.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the land asset.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
