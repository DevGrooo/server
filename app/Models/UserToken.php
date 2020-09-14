<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserToken extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'user_id', 'has_token', 'expired_at'
    ];

    protected $dates = [
        'expired_at',
    ];

    /**
     * Get the user that owns the user token.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    /**
     * Check api token exists and expired
     * 
     * @param string $token
     * @param UserToken $user_token
     * @return true|false
     */
    public static function checkToken($token, &$user_token = null)
    {
        // check token exists
        $user_token = UserToken::where('hash_token', self::_decryptHashToken($token))->first();
        if ($user_token) {
            // check token expired
            if (strtotime($user_token->expired_at) > time()) {
                return true;
            }
        }
        return false;
    }

    public static function isExistsTokenForUser($user_id, &$token = '')
    {
        // check token exists for user_id
        $user_token = UserToken::where('user_id', $user_id)->first();
        if ($user_token) {
            // check token expired
            if (strtotime($user_token->expired_at) > time()) {
                $token = UserToken::encryptHashToken($user_token->hash_token);
                return true;
            }
        }
        return false;
    }

    /**
     * Encypt hash_token
     * 
     * @return String
     */
    public static function encryptHashToken($token)
    {
        return Crypt::encrypt($token);
    }

    /**
     * Encypt hash_token
     * 
     * @return String
     */
    private static function _decryptHashToken($token)
    {
        return Crypt::decrypt($token);
    }

    public function generateToken()
    {
        return md5(substr(md5(rand(1,100000)), 0, rand(10,20)));
    }

    /**
     * @param integer $start timestamp
     */
    public function getExpiredAt($start)
    {
        return $start + config('token.expired_second');
    }
}
