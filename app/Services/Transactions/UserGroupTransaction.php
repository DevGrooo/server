<?php


namespace App\Services\Transactions;


use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;

class UserGroupTransaction extends Transaction
{
    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function create(array $params, bool $allow_commit)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        $user = Auth::user();
        try {
            $user_group = new UserGroup();
            $user_group->parent_id = $params['parent_id'];
            $user_group->name = $params['name'];
            $user_group->code = $params['code'];
            $user_group->status = $params['status'];
            $user_group->created_by = $user->id;
            $user_group->save();
            $response = ["user_group_id" => 1];
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
    public function update(array $params, bool $allow_commit)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        $user = Auth::user();
//        dd($user);
        $user_group = UserGroup::where('id', $params['user_group_id'])->first();
//        dd($user_group);
        try {
            if ($user_group) {
                $user_group->name = $params['name'];
                $user_group->code = $params['code'];
                $user_group->status = $params['status'];
                $user_group->updated_by = $user->id;
                $user_group->save();
            } else {
                $this->error('User group không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
