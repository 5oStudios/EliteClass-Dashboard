<?php

namespace App;

use App\Wallet;
use App\Affiliate;
use Carbon\Carbon;
use App\UserFingerprint;
use App\WalletTransactions;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;
use Lab404\Impersonate\Models\Impersonate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\EmailVerificationNotificationViaAPI;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasApiTokens;
    use HasRoles;
    use HasTranslations;
    use Impersonate;
    use SoftDeletes;

    public $translatable = ['fname', 'lname', 'short_info', 'address', 'detail'];

    public function toArray()
    {
        $attributes = parent::toArray();

        foreach ($this->getTranslatableAttributes() as $name) {
            $attributes[$name] = $this->getTranslation($name, app()->getLocale());
        }

        return $attributes;
    }

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname', 'email', 'password', 'lname', 'short_info', 'dob', 'doa', 'country_code', 'timezone', 'mobile', 'address', 'institute',
        'major', 'city_id', 'state_id', 'country_id', 'gender', 'pin_code', 'status', 'test_user', 'is_locked', 'blocked_count', 'is_allow_multiple_device',
        'verified', 'role', 'married_status','user_img', 'detail', 'two_factor_code', 'two_factor_expires_at', 'braintree_id', 'fb_url',
        'twitter_url', 'youtube_url', 'linkedin_url', 'email_verified_at', 'code', 'token', 'google_id', 'facebook_id', 'amazon_id', 'gitlab_id',
        'linkedin_id', 'twitter_id', 'jwt_token', 'zoom_email', 'referred_by', 'affiliate_id', 'google2fa_secret', 'google2fa_enable',
        'remember_token', 'vacation_start', 'vacation_end', 'age', 'main_category','scnd_category_id','sub_category','ch_sub_category',
        'notifications','updated_by','deleted_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        return ucwords("{$this->fname} {$this->lname}");
    }

    public function generateTwoFactorCode(): void
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorCode(): void
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public static function createReferCode()
    {
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            . '0123456789');
        shuffle($seed);
        $rand = '';
        $affiliate = Affiliate::first();
        $ref_id = $affiliate->ref_length;
        foreach (array_rand($seed, $ref_id) as $k) {
            $rand .= $seed[$k];
        }
        return Str::upper($rand);
    }

    public function sendEmailVerificationNotificationViaAPI()
    {
        // We override the default notification and will use our own
        $this->notify(new EmailVerificationNotificationViaAPI());
    }

    public function device_tokens()
    {
        return $this->hasMany('Laravel\Passport\Token', 'user_id')->where('revoked', "0")->whereDate('expires_at', '>', Carbon::now())->whereNotNull('player_device_id');
    }

    public function routeNotificationForFcm()
    {
        $list = $this->device_tokens();
        $tokens = $list->count() > 0 ? $list->pluck('player_device_id') : collect([]);
        return $tokens->toArray();
    }

    public function scopeActive($query)
    {
        $query->where('status', '1');
    }

    public function scopeExceptTestUser($query)
    {
        $query->where('test_user', '0');
    }

    public function country()
    {
        return $this->belongsTo(\App\Categories::class, 'main_category', 'id');
    }

    public function type()
    {
        return $this->belongsTo(\App\secondaryCategory::class, 'scnd_category_id', 'id');
    }

    public function stage()
    {
        return $this->belongsTo(\App\SubCategory::class, 'sub_category', 'id');
    }

    public function majorr()
    {
        return $this->belongsTo(\App\ChildCategory::class, 'ch_sub_category', 'id');
    }

    public function city()
    {
        return $this->belongsTo('App\Allcity', 'city_id', 'id')->withDefault();
    }

    public function courses()
    {
        return $this->hasMany('App\Course', 'user_id');
    }

    public function answer()
    {
        return $this->hasMany('App\Question', 'user_id');
    }

    public function announsment()
    {
        return $this->hasMany('App\Announcement', 'user_id');
    }

    public function review()
    {
        return $this->hasMany('App\ReviewRating', 'user_id');
    }

    public function reportreview()
    {
        return $this->hasMany('App\ReportReview', 'user_id');
    }

    public function viewprocess()
    {
        return $this->hasMany('App\ViewProcess', 'user_id');
    }

    public function wishlist()
    {
        return $this->hasMany('App\Wishlist', 'user_id');
    }

    public function blogs()
    {
        return $this->hasMany('App\Blog', 'user_id');
    }

    public function relatedcourse()
    {
        return $this->hasMany('App\RelatedCourse', 'user_id');
    }

    public function courseclass()
    {
        return $this->hasMany('App\CourseClass', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Order', 'user_id');
    }

    public function pending()
    {
        return $this->hasMany('App\PendingPayout', 'user_id');
    }

    public function liveclass()
    {
        return $this->hasMany('App\LiveCourse', 'user_id');
    }

    public function completed()
    {
        return $this->hasMany('App\CompletedPayout', 'user_id');
    }

    public function bundle()
    {
        return $this->hasMany('App\BundleCourse', 'user_id');
    }

    public function fingerprint()
    {
        return $this->hasOne('Laravel\Passport\Token','user_id')->whereNotNull('fpjsid')->latest();
    } 

    public function plans()
    {
        return $this->hasMany('App\PlanSubscribe', 'user_id');
    }

    public function routeNotificationForOneSignal()
    {
        $list = $this->device_tokens();
        $tokens = $list->count() > 0 ? array_values($list->pluck('player_device_id')->unique()->toArray()) : collect([]);
        $tok = $tokens;//->toArray();
        Log::info($tok);
        return $tok;
        // return ['include_external_user_ids' => [$this->id.""]];
    }

    private function checkWallet()
    {
        if ($this->id) {
            $w = Wallet::where('user_id', $this->id)->first();
            if ($w && $w->id) {
                return $w;
            } else {
                // dd($this,'In user model to update user balance while creating user wallet');
                 Wallet::create([
                                'user_id' => $this->id,
                                'balance' => 0,
                            ]);
            }
        }
    }

    public function wallet()
    {
        $this->checkWallet();
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    public function cartType($type, $id)
    {
        return $this->hasOne(Cart::class, 'user_id')->where($type . '_id', $id);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    public function topup_transactions()
    {
        return $this->hasOne(WalletTransactions::class, 'user_id')->where('detail', 'like', '%wallet%')->latest();
    }
}
