<?php

namespace App\Http\Controllers\Roles;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('role.view'), 403, __('User does not have the right permissions.'));

        $roles = DB::table('roles')->select('roles.id', 'roles.name');

        if ($request->ajax()) {
            return DataTables::of($roles)

            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->editColumn('action', 'roles.action')
            ->rawColumns(['name', 'action'])
            ->make(true);
        }

        return view('roles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!auth()->user()->can('role.create'), 403, __('User does not have the right permissions.'));

        $role_permission = Permission::select('name', 'id')->groupBy('name')->get();

        $custom_permission = array();

        foreach ($role_permission as $per) {
            $key = substr($per->name, 0, strpos($per->name, "."));

            if (str_starts_with($per->name, $key)) {
                $custom_permission[$key][] = $per;
            }
        }

        return view('roles.create', compact('custom_permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('role.create'), 403, __('User does not have the right permissions.'));

        $request->validate([
            'name' => 'required|without_spaces|unique:roles,name',
            'permissions' => 'required'
        ],
        [
            'name.required' => __('Role name is required.'),
            'name.without_spaces' => 'Whitespace not allowed.',
            'name.unique'   => __('Role name already taken.'),
            'permissions.required'   => __('At least one permission must be assigned to a new role.')
        ]);

        $role = Role::create(['name' => strtolower($request->name)]);

        if ($request->permissions) {
            foreach ($request->permissions as $key => $value) {
                $role->givePermissionTo($value);
            }
        }

        Session::flash('success', trans('Roles has been created Successfully'));
        return redirect(route('roles.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!auth()->user()->can('role.edit'), 403, __('User does not have the right permissions.'));

        $role = Role::with('permissions')->find($id);

        $role_permission = Permission::select('name', 'id')->get();

        $custom_permission = array();

        foreach ($role_permission as $per) {
            $key = substr($per->name, 0, strpos($per->name, "."));

            if (str_starts_with($per->name, $key)) {
                $custom_permission[$key][] = $per;
            }
        }

        return view('roles.view', compact('role_permission', 'role', 'custom_permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!auth()->user()->can('role.edit'), 403, __('User does not have the right permissions.'));

        $role = Role::with('permissions')->find($id);

        $role_permission = Permission::select('name', 'id')->get();

        $custom_permission = array();

        foreach ($role_permission as $per) {
            $key = substr($per->name, 0, strpos($per->name, "."));

            if (str_starts_with($per->name, $key)) {
                $custom_permission[$key][] = $per;
            }
        }

        // if (in_array($id, ['1','2','3'])) {
        //     Session::flash('success', trans('System role can not be edit'));
        // }

        return view('roles.edit', compact('role_permission', 'role', 'custom_permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('role.edit'), 403, __('User does not have the right permissions.'));

        if (in_array($id, ['1','2','3'])) {
            // Session::flash('success', trans('System role cannot be edit !'));
            return redirect(route('roles.index'));
        }

        $role = Role::find($id);

        $request->validate([
            'name' => 'required|without_spaces|unique:roles,name,' . $id
        ],
        [
            'name.required' => __('Role name is required !'),
            'name.unique'   => __('Role name already taken !')
        ]);

        $role->name = strtolower($request->name);

        $role->save();

        $role->syncPermissions($request->permissions);

        Session::flash('success', trans('Roles has been updated Successfully'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('role.delete'), 403, __('User does not have the right permissions.'));

        $role = Role::find($id);

        if (isset($role)) {
            $users = User::role($role->name)->get();
            $role->permissions()->detach();

            foreach ($users as $user) {
                $user->role = 'user';
                $user->save();
                $user->syncRoles('user');
                // $user->roles()->detach();
            }

            $role->delete();
            session()->flash('delete', trans('Role has been deleted'));
            return back();
        } else {
            session()->flash('delete', trans('Role not found'));
            return back();
        }
    }

    public function createPermission(Request $request)
    {

        Permission::create([
            'name' => $request->name,
        ]);

        echo __("Created");

        return back();
    }

    public function bulkPermission(Request $request)
    {
        Permission::create([
            'name' => $request->name . '.view',
        ]);

        Permission::create([
            'name' => $request->name . '.create',
        ]);

        Permission::create([
            'name' => $request->name . '.edit',
        ]);

        Permission::create([
            'name' => $request->name . '.delete',
        ]);

        echo __("Created");

        return back();
    }
}
