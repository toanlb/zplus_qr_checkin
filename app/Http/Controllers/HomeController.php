<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the home page request and redirect based on user role.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Nếu người dùng là admin, chuyển hướng đến dashboard
        if (auth()->user()->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // Ngược lại, hiển thị trang home của thành viên
        return view('member.home');
    }
}