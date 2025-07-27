<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AssetDocument extends Model
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
        'asset_id',
        'document_name',
        'document_type',
        'file_path',
        'issue_date',
        'expiry_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
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
     * Get the land asset that owns the document.
     */
    public function asset()
    {
        return $this->belongsTo(LandAsset::class, 'asset_id');
    }

    /**
     * Check if the document is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return now()->greaterThan($this->expiry_date);
    }

    /**
     * Get the expiry status of the document.
     *
     * @return string
     */
    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return 'no_expiry';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        if (now()->diffInDays($this->expiry_date) <= 30) {
            return 'expiring_soon';
        }

        return 'valid';
    }
}
