<?php

namespace App\Services\Transactions;

use App\Models\Country;

class CountryTransaction extends Transaction {
    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function add($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
          $country_code = Country::where('code','=',$params['code'])->first();
          $country_name = Country::where('name','=',$params['name'])->first();
          if($country_code === null &&  $country_name === null ){
              $new_country = new Country();
              $new_country->name = $params['name'];
              $new_country->code = $params['code'];
              $new_country->status = $params['status'];
              $new_country->created_by = $params['created_by'];
              $create = $new_country->save();
              if(!$create){
                  $this->error('Có lỗi xảy ra khi thêm quốc gia !');
              }
          }else if ($country_code != null) {
              $this->error('Mã code của quốc gia này đã bị trùng');
          } else if ($country_name != null){
              $this->error('Đã tồn tại quốc gia trong dữ liệu');
          }
        }catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function update($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $country = Country::where('id', '=' , $params['country_id'])->first();
            if ( $country ){
                $country->name = $params['name'];
                $country_name = Country::select('id','name')->whereNotIn('id',[$params['country_id']])->where('name', $params['name'])->first();
                if ($country_name == null){
                    $country->code = $params['code'];
                }else{
                    $this->error('Đã tồn tại quốc gia trong dữ liệu');
                }
                $country->updated_by = $params['update_by'];
                $update = $country->save();
                if(!$update){
                    $this->error('Có lỗi xảy ra khi cập nhật quốc gia !');
                }
            }else {
                $this->error('Không tìm thấy quốc gia này');
            }
        }catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function lock($params, $allow_commit = false){
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $country = Country::where('id', '=' , $params['country_id'])->first();
            if ($country) {
                if ($country::STATUS_ACTIVE) { //Lock user
                    $country->status = Country::STATUS_LOCK;
                    $country->updated_by = $params['updated_by'];
                    $lock = $country->save();
                    if(!$lock){
                        $this->error('Có lỗi xảy ra khi khóa quốc gia !');
                    }
                } else {
                    $this->error('Quốc gia này không được hỗ trợ');
                }
            } else {
                $this->error('Không tìm thấy quốc gia này');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function active($params, bool $allow_commit = true){
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $country = Country::where('id', '=' , $params['country_id'])->first();
            if($country){
                if ($country->status == Country::STATUS_LOCK) { //Lock country
                    $country->status = Country::STATUS_ACTIVE;
                    $country->updated_by = $params['updated_by'];
                    $active = $country->save();
                    if(!$active){
                        $this->error('Có lỗi xảy ra khi mở khóa quốc gia !');
                    }
                } else {
                    $this->error('Quốc gia này không được hỗ trợ');
                }
            }else {
                $this->error('Không tìm thấy quốc gia này');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
