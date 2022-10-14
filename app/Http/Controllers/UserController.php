<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\Validator;
use App\Helper\Media;
use App\Models\Common;
use App\Models\Role;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function list(Request $request)
    {
        try {

            if ($request->ajax()) {
                $users = User::with('get_role')->where('id', '!=', Auth::user()->id)->orderBy('id', 'desc')->get();;
                return DataTables::of($users)
                    ->addColumn('image', function ($row) {
                        return '<span class="userListImg"><img src="' . asset('' . $row->avatar) . '" class="user-avatar" alt="user-avatar"></span>';
                    })
                    ->addColumn('name', function ($row) {
                        return $row->name;
                    })
                    ->addColumn('email', function ($row) {
                        return $row->email;
                    })
                    ->addColumn('ph_no', function ($row) {
                        return $row->phone;
                    })
                    ->addColumn('role', function ($row) {
                        return $row->get_role->name;
                    })
                    ->addColumn('status', function ($row) {
                        $status = '';
                        if ($row->status == 0) {
                            $status = '<span class="badge badge-danger">Inactive</span>';
                        }
                        if ($row->status == 1) {
                            $status = '<span class="badge badge-success">Active</span>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($row) {
                        $dropdown = '<a class="dropdown-item" href="' . route('admin.users.edit', [base64_encode($row->id)]) . '">Edit</a>
                        <a class="dropdown-item" href="#" id="delete_user" data-url="' . route('admin.users.delete', [base64_encode($row->id)]) . '">Delete</a>
                        <a class="dropdown-item" href="' . route('admin.users.manageRole', [base64_encode($row->id)]) . '">Manage Role</a>';
                        if ($row->status == 1) {
                            $dropdown .= '<a class="dropdown-item" id="change_status" href="#" data-status="0"  data-url="' . route('admin.users.changeStatus', [base64_encode($row->id), 0]) . '">Block</a>';
                        } else {
                            $dropdown .= '<a class="dropdown-item" id="change_status" href="#" data-status="1" data-url="' . route('admin.users.changeStatus', [base64_encode($row->id), 1]) . '">Unblock</a>';
                        }
                        $btn = '<div class="dropdown">
                                    <div class="actionList " id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>

                                    </div>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        ' . $dropdown . '
                                    </div>
                                </div>
                                ';

                        return $btn;
                    })
                    ->rawColumns(['action', 'image', 'status'])
                    ->make(true);
            }

            $data = [];
            $data['heading'] = 'Users List';
            $data['breadcrumb'] = ['Users', 'List'];
            return view('admin.users.list', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
    public function profile()
    {
        $data = [];
        $data['heading'] = 'Settings';
        $data['breadcrumb'] = ['Settings'];
		
		$branch_id		= Session::get('branch_id');
        //$supplier_id	= Session::get('adminId');
		
		
		$supplier		= User::find($branch_id);
		$admin_email	= $supplier->email;
		
		
		$data['company_name'] 		= Common::get_user_settings($where=['option_name'=>'company_name'],$branch_id);
		$data['company_address'] 	= Common::get_user_settings($where=['option_name'=>'company_address'],$branch_id);
		$data['address1'] 			= Common::get_user_settings($where=['option_name'=>'address1'],$branch_id);
		$data['address2'] 			= Common::get_user_settings($where=['option_name'=>'address2'],$branch_id);
		$data['phone'] 				= Common::get_user_settings($where=['option_name'=>'phone'],$branch_id);
		$data['company_licensee'] 	= Common::get_user_settings($where=['option_name'=>'company_licensee'],$branch_id);
		$data['admin_email'] 		= $admin_email;
		
		
		//echo '<pre>';print_r($data);exit;
		
		
		
        return view('admin.profile.edit', compact('data'));
    }
    public function profile_edit(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                // dd($request->all());
                $user_id = Auth::user()->id;
                $validator = Validator::make($request->all(), [
                    'user_avatar' => 'nullable|mimes:jpeg,jpg,png',
                    'email' => 'required|email|unique:users,email,' . $user_id,
                    'phone' => 'required|numeric|unique:users,phone,' . $user_id,
                    'full_name' => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                $array = [
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ];
                if ($file = $request->file('user_avatar')) {

                    $fileData = Media::uploads($file, 'uploads/avatar');
                    $array['avatar'] = $fileData['filePath'];
                }
                User::find($user_id)->update($array);
                return redirect()->back()->with('success', 'Profile updated successfully');
            }
            $data = [];
            $data['heading'] = 'Profile Edit';
            $data['breadcrumb'] = ['Profile', 'Edit'];
            return view('admin.profile.edit', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

    public function manage_role(Request $request, $id)
    {
        try {
            $user_id = base64_decode($id);
            $data = [];
            $data['heading'] = 'Manage Role';
            $data['breadcrumb'] = ['Users', 'Manage Role'];
            $data['users'] = User::find($user_id);
            $data['role'] = Role::all();
            return view('admin.users.manage_role', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }

    public function set_role(Request $request, $id, $role_id)
    {
        try {
            User::find($id)->update([
                'role' => $role_id
            ]);
            return redirect()->back()->with('success', 'Role assigned successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {

        try {
            $user_id = base64_decode($id);
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'user_avatar' => 'nullable|mimes:jpeg,jpg,png',
                    'email' => 'required|email|unique:users,email,' . $user_id,
                    'phone' => 'required|numeric|unique:users,phone,' . $user_id,
                    'full_name' => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                $array = [
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ];
                if ($file = $request->file('user_avatar')) {

                    $fileData = Media::uploads($file, 'uploads/avatar');
                    $array['avatar'] = $fileData['filePath'];
                }
                User::find($user_id)->update($array);
                return redirect()->back()->with('success', 'Profile updated successfully');
            }
            $data = [];
            $data['heading'] = 'Edit';
            $data['breadcrumb'] = ['Users', 'Edit'];
            $data['users'] = User::find($user_id);
            return view('admin.users.edit', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
    public function delete($id)
    {
        try {
            $id = base64_decode($id);
            User::find($id)->delete();
            return redirect()->back()->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
    public function change_status($id, $status)
    {
        try {
            $id = base64_decode($id);
            User::find($id)->update([
                'status' => $status
            ]);
            if ($status == 0)
                return redirect()->back()->with('success', 'User blocked successfully');
            else
                return redirect()->back()->with('success', 'User unblocked successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try later. ' . $e->getMessage());
        }
    }
}
