<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterUserController extends Controller
{
    public function index()
    {
        $users = User::with('hub')
            ->orderBy('name')
            ->get();

        $hubs = Hub::where('is_active',1)
            ->orderBy('hub_name')
            ->get();

        return view('master.user',compact(
            'users',
            'hubs'
        ));
    }

   public function store(Request $request)
{
    $request->validate([

        'nik' => 'required|unique:users',

        'name' => 'required',

        'email' => [
            'required',
            'email',
            'unique:users',
            'regex:/@spxexpress\.com$/'
        ],

        'password' => 'required|min:6',

        'hub_id' => 'required|exists:hubs,id',

        'role' => 'required'

    ],[
        'email.regex' => 'Gunakan email kantor @spxexpress.com'
    ]);

    User::create([

        'nik' => $request->nik,

        'name' => $request->name,

        'email' => strtolower($request->email),

        'password' => Hash::make($request->password),

        'hub_id' => $request->hub_id,

        'role' => $request->role,

        'is_active' => 1

    ]);

    return back()->with(
        'success',
        'User berhasil ditambahkan.'
    );
}
    public function update(Request $request,User $user)
    {

        $request->validate([

            'nik'=>'required|unique:users,nik,'.$user->id,

            'name'=>'required',

            'email'=>[
                'required',
                'email',
                'unique:users,email,'.$user->id,
                'regex:/@spxexpress\.com$/'
            ],

            'hub_id'=>'required|exists:hubs,id',

            'role'=>'required'

        ],[
                'nik.required' => 'NIK wajib diisi.',
    'nik.unique' => 'NIK sudah digunakan.',
    'email.required' => 'Email wajib diisi.',
    'email.unique' => 'Email sudah digunakan.',
            'email.regex'=>'Gunakan email kantor @spxexpress.com'
        ]);

        $user->nik=$request->nik;
        $user->name=$request->name;
        $user->email=strtolower($request->email);
        $user->hub_id=$request->hub_id;
        $user->role=$request->role;

        if($request->filled('password'))
        {
            $user->password=
                Hash::make($request->password);
        }

        $user->hub_id = $request->hub_id;
$user->role = $request->role;
$user->is_active = $request->is_active;

if ($request->filled('password')) {
    $user->password = Hash::make($request->password);
}

$user->save();

        return back()->with(
            'success',
            'User berhasil diupdate.'
        );

    }

     public function toggle(User $user)
    {

        if(auth()->id()==$user->id){

            return back()->with(
                'error',
                'Tidak dapat menonaktifkan akun sendiri.'
            );

        }

        $user->is_active=!$user->is_active;

        $user->save();

        return back()->with(
            'success',
            'Status akun berhasil diperbarui.'
        );

    }

    public function destroy(User $user)
    {

        if(auth()->id()==$user->id){

            return back()->with(
                'error',
                'Tidak dapat menghapus akun sendiri.'
            );

        }

        $user->delete();

        return back()->with(
            'success',
            'User berhasil dihapus.'
        );

    }


}