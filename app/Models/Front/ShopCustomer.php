<?php

namespace App\Models\Front;

use App\Models\Front\ShopEmailTemplate;
use App\Models\Front\ShopCustomerAddress;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Front\ShopCustomFieldDetail;
use Illuminate\Auth\AuthenticationException;

class ShopCustomer extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'shop_customer';
    protected $guarded = [];
    protected $guard_name = 'web_api';
    private static $profile = null;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $appends = [
        'name',
    ];
    public function orders()
    {
        return $this->hasMany(ShopOrder::class, 'customer_id', 'id');
    }

    /*
    Get store
    */
    public function store()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    
    public function addresses()
    {
        return $this->hasMany(ShopCustomerAddress::class, 'customer_id', 'id');
    }

    /**
     * Send email reset password
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    public function sendPasswordResetNotification($token)
    {
        $checkContent = (new ShopEmailTemplate)->where('group', 'forgot_password')->where('status', 1)->first();
        if ($checkContent) {
            $content = $checkContent->text;
            $dataFind = [
                '/\{\{\$title\}\}/',
                '/\{\{\$reason_sendmail\}\}/',
                '/\{\{\$note_sendmail\}\}/',
                '/\{\{\$note_access_link\}\}/',
                '/\{\{\$reset_link\}\}/',
                '/\{\{\$reset_button\}\}/',
            ];
            $url = lc_route('password.verify', ['token' => $token]);
            $dataReplace = [
                trans('email.forgot_password.title'),
                trans('email.forgot_password.reason_sendmail'),
                trans('email.forgot_password.note_sendmail', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]),
                trans('email.forgot_password.note_access_link', ['reset_button' => trans('email.forgot_password.reset_button'), 'url' => $url]),
                $url,
                trans('email.forgot_password.reset_button'),
            ];
            $content = preg_replace($dataFind, $dataReplace, $content);
            $dataView = [
                'content' => $content,
            ];

            $config = [
                'to' => $this->getEmailForPasswordReset(),
                'subject' => trans('email.forgot_password.reset_button'),
            ];

            lc_send_mail('templates.' . lc_store('template') . '.mail.forgot_password', $dataView, $config, $dataAtt = []);
        }

    }

    /*
    Full name
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;

    }



    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($customer) {

            //Delete custom field
            (new ShopCustomFieldDetail)
                ->join('shop_custom_field', 'shop_custom_field.id', 'shop_custom_field_detail.custom_field_id')
                ->select('code', 'name', 'text')
                ->where('shop_custom_field_detail.rel_id', $customer->id)
                ->where('shop_custom_field.type', 'customer')
                ->delete();
            }
        );
    }


    /**
     * Update info customer
     * @param  [array] $dataUpdate
     * @param  [int] $id
     */
    public static function updateInfo($dataUpdate, $id)
    {
        $dataUpdate = lc_clean($dataUpdate, 'password');
        $obj = self::find($id);
        return $obj->update($dataUpdate);
    }

    /**
     * Create new customer
     * @return [type] [description]
     */
    public static function createCustomer($dataInsert)
    {
        $dataClean = lc_clean($dataInsert, 'password');
        $dataAddress = [
            'first_name'      => $dataClean['first_name'] ?? '',
            'last_name'       => $dataClean['last_name'] ?? '',
            'first_name_kana' => $dataClean['first_name_kana'] ?? '',
            'last_name_kana'  => $dataClean['last_name_kana'] ?? '',
            'postcode'        => $dataClean['postcode'] ?? '',
            'address1'        => $dataClean['address1'] ?? '',
            'address2'        => $dataClean['address2'] ?? '',
            'address3'        => $dataClean['address3'] ?? '',
            'country'         => $dataClean['country'] ?? '',
            'phone'           => $dataClean['phone'] ?? '',
        ];
        $user = self::create($dataClean);
        $address = $user->addresses()->save(new ShopCustomerAddress($dataAddress));
        $user->address_id = $address->id;
        $user->save();
        return $user;
    }

    /**
     * Get address default of user
     *
     * @return  [collect]
     */
    public function getAddressDefault() {
        return (new ShopCustomerAddress)->where('customer_id', $this->id)
            ->where('id', $this->address_id)
            ->first();
    }

    public function profile() {
        if (self::$profile === null) {
            self::$profile = Auth::user();
        }
        return self::$profile;
    }

    /**
     * Check customer has Check if the user is verified
     *
     * @return boolean
     */
    public function isVerified() {
        return ! is_null($this->email_verified_at)  || $this->provider_id ;
    }

    /**
     * Check customer need verify email
     *
     * @return boolean
     */
    public function hasVerifiedEmail() {
        return !$this->isVerified() && lc_config('customer_verify');
    }
    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerify() {

        if ($this->hasVerifiedEmail()) {
            
            $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'customer.verify_process',
                \Carbon\Carbon::now()->addMinutes(config('auth.verification', 60)),
                [
                    'id' => $this->id,
                    'token' => sha1($this->email),
                ]
            );

            $checkContent = (new ShopEmailTemplate)->where('group', 'customer_verify')->where('status', 1)->first();
            if ($checkContent) {
                $content = $checkContent->text;
                $dataFind = [
                    '/\{\{\$title\}\}/',
                    '/\{\{\$reason_sendmail\}\}/',
                    '/\{\{\$note_sendmail\}\}/',
                    '/\{\{\$note_access_link\}\}/',
                    '/\{\{\$url_verify\}\}/',
                    '/\{\{\$button\}\}/',
                ];
                $dataReplace = [
                    trans('email.verification_content.title'),
                    trans('email.verification_content.reason_sendmail'),
                    trans('email.verification_content.note_sendmail', ['count' => config('auth.verification')]),
                    trans('email.verification_content.note_access_link', ['button' => trans('email.verification_content.button'), 'url' => $url]),
                    $url,
                    trans('email.verification_content.button'),
                ];
                $content = preg_replace($dataFind, $dataReplace, $content);
                $dataView = [
                    'content' => $content,
                ];
    
                $config = [
                    'to' => $this->email,
                    'subject' => trans('email.verification_content.button'),
                ];
    
                lc_send_mail('templates.' . lc_store('template') . '.mail.customer_verify', $dataView, $config, $dataAtt = []);
                return true;
            }
        } 
        return false;
    }

    /**
     * Get all custom fields
     *
     * @return void
     */
    public function getCustomFields() {
        $data =  (new ShopCustomFieldDetail)
            ->join('shop_custom_field', 'shop_custom_field.id', 'shop_custom_field_detail.custom_field_id')
            ->select('code', 'name', 'text')
            ->where('shop_custom_field_detail.rel_id', $this->id)
            ->where('shop_custom_field.type', 'customer')
            ->where('shop_custom_field.status', '1')
            ->get()
            ->keyBy('code');
        return $data;
    }

    /**
     * Get custom field
     *
     * @return void
     */
    public function getCustomField($code = null) {
        $data =  (new ShopCustomFieldDetail)
            ->join('shop_custom_field', 'shop_custom_field.id', 'shop_custom_field_detail.custom_field_id')
            ->select('code', 'name', 'text')
            ->where('shop_custom_field_detail.rel_id', $this->id)
            ->where('shop_custom_field.type', 'customer')
            ->where('shop_custom_field.status', '1');
        if ($code) {
            $data = $data->where('shop_custom_field.code', $code);
        }
        $data = $data->first();
        return $data;
    }
}
