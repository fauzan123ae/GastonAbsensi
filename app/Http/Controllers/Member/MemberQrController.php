<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MemberQrController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('member.qr', compact('user'));
    }
}
