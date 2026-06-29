<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'nickname.required' => 'Informe o Nickname.',
            'password.required' => 'Informe a senha.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput(['login_nickname' => $request->string('nickname')->toString()])
                ->with('login_error', $validator->errors()->first());
        }

        $credentials = $validator->validated();

        if (! Auth::attempt($credentials)) {
            return back()
                ->withInput(['login_nickname' => $request->string('nickname')->toString()])
                ->with('login_error', 'Digite novamente. Nickname ou senha incorretos.');
        }

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('login_success', 'Login efetuado com sucesso.');
    }
}
