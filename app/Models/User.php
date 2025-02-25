<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AccountStatus;
use App\Enums\KYCStatus;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id The unique identifier of the user
 * @property string $username Unique username for the user
 * @property string $first_name User's first name
 * @property string $last_name User's last name
 * @property string $phone_number User's phone number
 * @property string $whatsapp_number User's whatsapp number
 * @property string $email Unique email address of the user
 * @property Carbon|null $email_verified_at Timestamp when the user's email was verified
 * @property KYCStatus|null $kyc_status
 * @property AccountStatus|null $account_status
 * @property Carbon|null $banned_till
 * @property string $bank_name
 * @property string $bank_account_name
 * @property string $bank_account_number
 * @property string $referral_code
 * @property string $password Hashed password of the user
 * @property string|null $remember_token Token for "remember me" functionality
 * @property Carbon $created_at Timestamp when the user was created
 * @property Carbon $updated_at Timestamp when the user was last updated
 *
 * @property-read Collection<CryptoTrade> $withdrawals Collection of user's withdrawal requests
 * @property-read string $full_name Concatenated first and last name
 * @property-read KycVerification $kyc
 *
 */
class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    protected static function boot()
    {
        parent::boot();

        self::created(function (User $user) {
            Wallet::query()->create(['user_id' => $user->id, 'balance' => 0]);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'phone_number',
        'whatsapp_number',
        'email',
        'password',
        'account_status',
        'ban_reason',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'referral_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'account_status' => AccountStatus::class,
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "$this->first_name $this->last_name";
    }

    public function avatar(): string
    {
        //        if ($this->hasMedia('avatar')) {
        //            return $this->getFirstMediaUrl('avatar');
        //        }

        // Create an SHA256 hash of the final string
        $hash = hash('sha256', $this->email);

        // default
        $default = asset('images/avatar.png');

        // Grab the actual image URL
        return "https://www.gravatar.com/avatar/$hash?d=$default";
    }

    public function kycVerification(): HasOne
    {
        return $this->hasOne(KycVerification::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return collect([$this->last_name, $this->first_name])
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
        // return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar();
    }

    public function getFilamentName(): string
    {
        return "$this->full_name";
    }
}
