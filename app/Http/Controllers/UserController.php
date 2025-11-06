<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Enums\ResponseMessage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use ResponseAPI;
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'nullable|exists:businesses,id',
            'location_id' => 'nullable|exists:locations,id'
        ], $this->validationMessage());

        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        $wh = [];
        if (!empty($request->business_id)) {
            $wh[] = ['business_id', $request->business_id];
        }
        if (!empty($request->location_id)) {
            $wh[] = ['location_id', $request->location_id];
        }
        $roles = User::with(['roles','roles.permissions'])->where($wh)->get();
        return $this->success($roles, ResponseMessage::FETCHED, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'business_id' => 'nullable|exists:businesses,id',
            'location_id' => 'nullable|exists:locations,id',
            'role_id'    => 'required|exists:roles,id'
        ], $this->validationMessage());

        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        DB::beginTransaction();

        try {
            $obj = [
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'business_id' => $request->business_id,
                'location_id' => $request->location_id
            ];
            $user = User::create($obj);
            if (!empty($request->role_id)) {
                $role = Role::where('id', $request->role_id)->pluck('name');
                $user->syncRoles([$role->name]);
            }
            $data = User::with(['roles','roles.permissions'])->find($user->id);
            DB::commit();
            return $this->success(
                $data,
                ResponseMessage::SAVE,
                200
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    public function getById($user_id)
    {
        $user = User::with(['roles','roles.permissions'])->find($user_id);
        return $this->success($user, ResponseMessage::FETCHED_DETAIL, 200);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'       => 'required|exists:users,id',
            'name'     => 'sometimes|string|max:100',
            'email'    => 'sometimes|email|unique:users,email,' . $request->id,
            'password' => 'nullable|string|min:6',
            'business_id' => 'nullable|exists:businesses,id',
            'location_id' => 'nullable|exists:locations,id',
            'role_id'  => 'sometimes|exists:roles,id',
        ], $this->validationMessage());
        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->id);
            $obj = [
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => $request->password ? Hash::make($request->password) : $user->password,
                'business_id' => $request->business_id,
                'location_id' => $request->location_id
            ];
            $user->update($obj);
            if ($request->filled('role_id')) {
                $role = Role::findOrFail($request->role_id);
                $user->syncRoles([$role->name]);
            }
            DB::commit();
            return $this->success(
                $user,
                ResponseMessage::UPDATE,
                200
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
        ], $this->validationMessage());
        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        $user = User::find($request->id);
        $user->delete();
        return $this->success([], ResponseMessage::DELETE, 200);
    }
}
