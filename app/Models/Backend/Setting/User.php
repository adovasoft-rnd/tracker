<?php

namespace App\Models\Backend\Setting;

use App\Supports\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Preference
 * @package App\Models\Backend\Auth
 */
class User extends Authenticatable implements HasMedia, Auditable
{
    use AuditableTrait, HasFactory, Notifiable, InteractsWithMedia, HasRoles, Sortable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'mobile',
        'password',
        'remarks',
        'home_page',
        'locale',
        'enabled',
        'email_verified_at',
        'parent_id',
        'created_by',
        'updated_by',
        'deleted_by'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'force_pass_reset' => 'bool'
    ];

    /************************ Audit Relations ************************/

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /************************ Other Methods ************************/
    /**
     * Register profile Image Media Collection
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->useDisk('avatar')
            ->useFallbackUrl(Constant::USER_PROFILE_IMAGE)
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg'])
            ->singleFile();
    }

    /**
     * Verify if current user as super admin role
     *
     * @return bool
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('Super Administrator');
    }

    /**
     * Return all Role ID's of a user
     *
     * @return array
     */
    public function getRoleIdsAttribute(): array
    {
        return $this->roles()->pluck('id')->toArray();
    }

    /**
     * Return all Permission ID's of a user
     *
     * @return array
     */
    public function getPermissionIdsAttribute(): array
    {
        return $this->permissions()->pluck('id')->toArray();
    }

    public function receivers()
    {
        return $this->hasMany(User::class, 'id', 'parent_id');
    }
}
