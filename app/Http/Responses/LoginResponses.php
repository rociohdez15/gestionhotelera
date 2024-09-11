<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // Redirige a la URL guardada en la sesión o a una ruta predeterminada
        return redirect()->intended($request->session()->get('url.intended', '/dashboard'));
    }
}
