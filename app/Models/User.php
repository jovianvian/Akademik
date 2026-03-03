<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'status',
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string ...$roles): bool
    {
        $roleName = $this->role?->role_name;

        return in_array($roleName, $roles, true);
    }

    public function abilities(): array
    {
        $roleName = $this->role?->role_name;
        if (! $roleName) {
            return [];
        }

        if (Schema::hasTable('permissions') && Schema::hasTable('role_permissions') && $this->role_id) {
            $codes = DB::table('role_permissions as rp')
                ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
                ->where('rp.role_id', $this->role_id)
                ->pluck('p.kode')
                ->all();

            if (! empty($codes)) {
                return $codes;
            }
        }

        return config("permissions.roles.{$roleName}.abilities", []);
    }

    public function hasAbility(string $ability): bool
    {
        return in_array($ability, $this->abilities(), true);
    }
}
