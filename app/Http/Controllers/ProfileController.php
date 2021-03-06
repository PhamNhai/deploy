<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\File;
use App\Models\Address;
use App\Models\Order;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $profile = Auth::user();
        $address = Address::where('user_id',$profile->id)->get();
        $order = Order::where('user_id',$profile->id)->get();


        return view('profile', compact('profile', 'address', 'order'));
    }

    public function getEditProfile()
    {
        $user = Auth::user();
        return view('edit_profile',['profile' => $user]);
    }

    public function postProfile(Request $request )
    {
        $this->validate($request,[
            'name' => 'required|min:3',
        ],[
            'name.required' => 'Bạn chưa nhập tên người dùng',
            'name.min' => 'Tên người dùng phải có ít nhất 3 ký tự'
        ]);
        $this->validate($request,[
            'password' => 'max:62',
            'passwordAgain' => 'same:password',
        ],[
            'passwordAgain.required' => 'Bạn chưa nhập lại mật khẩu',
            'passwordAgain.same' => 'Password không giống nhau'
        ]);
        $user = Auth::user();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->subcribe = $request->subcribe;
        $user->email = $request->email;
        // if ($request->avatar_image_link) {
        //     $user->avatar_image_link = $request->avatar_image_link;
        // }
        if ($request->avatar_image_link) {
            $image_old = Auth::user()->avatar_image_link;
            File::delete('assets/uploads/' . $image_old);
            $file = $request->file('avatar_image_link');
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move('assets/uploads' , $name);
            $user->avatar_image_link = $name;
        }
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return redirect('profile')->with('thongbao','Bạn đã sửa thành công');
    }
}
