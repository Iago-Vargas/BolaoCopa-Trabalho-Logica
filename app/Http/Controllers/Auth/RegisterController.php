<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $words = preg_split('/\s+/', trim((string) $value));

                    if (count(array_filter($words)) < 2) {
                        $fail('Informe o nome composto, com nome e sobrenome.');
                    }
                },
            ],
            'nickname' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'nickname')],
            'password' => ['required', 'string', 'min:6', 'confirmed', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'name.required' => 'Informe seu nome.',
            'nickname.required' => 'Informe seu nickname.',
            'nickname.alpha_dash' => 'Use apenas letras, numeros, traco e underline no nickname.',
            'nickname.unique' => 'Esse nickname ja esta em uso.',
            'password.required' => 'Informe sua senha.',
            'password.min' => 'A senha precisa ter no minimo 6 caracteres.',
            'password.confirmed' => 'A confirmacao da senha nao confere.',
            'password.regex' => 'A senha precisa ter pelo menos um caractere especial.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']) + ['form_mode' => 'register']);
        }

        User::create([
            'name' => $request->string('name')->trim()->toString(),
            'nickname' => $request->string('nickname')->trim()->toString(),
            'password' => $request->string('password')->toString(),
        ]);

        return redirect('/')
            ->with('registered_success', 'Cadastro realizado com sucesso.');
    }
}
