<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_group' => $this->user_group()->select(['id', 'name', 'code'])->getResults(),
            'ref' => $this->_getRef(),
            'username' => $this->username,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'status' => $this->status,
            'status_name' => $this->_getStatusName(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected function _getRef() {
        if ($this->ref_type !== '') {
            return $this->ref()->select(['id', 'name'])->getResults();
        }
        return null;
    }

    protected function _getStatusName() {
        $list_status = User::getListStatus();
        return @list_status[$this->status];
    }
}
