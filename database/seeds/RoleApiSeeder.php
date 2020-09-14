<?php

require_once('MySeeder.php');

class RoleApiSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->_mappingRoleBasicAndApi();
    }

    /**
     * Mapping roles basic with api
     */
    private function _mappingRoleBasicAndApi()
    {
        $query = "
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user'), (SELECT id FROM api WHERE code = 'UserController@getStatus'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user'), (SELECT id FROM api WHERE code = 'UserController@getUserGroupList'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user'), (SELECT id FROM api WHERE code = 'UserController@getList'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/create'), (SELECT id FROM api WHERE code = 'UserController@create'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/create'), (SELECT id FROM api WHERE code = 'UserController@getUserGroupList'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/create'), (SELECT id FROM api WHERE code = 'UserController@getStatus'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/create'), (SELECT id FROM api WHERE code = 'UserController@getRefList'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/detail'), (SELECT id FROM api WHERE code = 'UserController@detail'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/update'), (SELECT id FROM api WHERE code = 'UserController@update'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/update'), (SELECT id FROM api WHERE code = 'UserController@detail'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/update'), (SELECT id FROM api WHERE code = 'UserController@getUserGroupList'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/update'), (SELECT id FROM api WHERE code = 'UserController@getRefList'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/update'), (SELECT id FROM api WHERE code = 'UserController@getStatus'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/lock'), (SELECT id FROM api WHERE code = 'UserController@lock'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/active'), (SELECT id FROM api WHERE code = 'UserController@active'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/update-role'), (SELECT id FROM api WHERE code = 'UserController@updateRole'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/update-role'), (SELECT id FROM api WHERE code = 'UserController@detail'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/update-role'), (SELECT id FROM api WHERE code = 'UserGroupController@getRoleList'));
        INSERT INTO role_api(role_id, api_id) VALUES((SELECT id FROM roles WHERE code = '/user/reset-password'), (SELECT id FROM api WHERE code = 'UserController@resetPassword'));
        ";
        $this->_executeMultiRaw($query);
    }
}
