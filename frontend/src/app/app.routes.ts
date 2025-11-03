import { Routes } from '@angular/router';
import { Register } from './features/auth/register/register';
import { Login } from './features/auth/login/login';
import { ForgetPass } from './features/auth/forget-pass/forget-pass';
import { VerCode } from './features/auth/ver-code/ver-code';
import { ResetPass } from './features/auth/reset-pass/reset-pass';
export const routes: Routes = [
    {path : '', redirectTo :'login', pathMatch :'full'},
    {path : 'register', component: Register},
    {path :'login', component :Login},
    {path :'forget-pass',component :ForgetPass},
    {path :'ver-code', component : VerCode},
    {path :'reset-pass', component :ResetPass}
];
