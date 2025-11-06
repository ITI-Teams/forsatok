import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { guestGuard } from './core/guards/guest.guard';
// Import Auth Pages
import { Register } from './features/auth/register/register';
import { Login } from './features/auth/login/login';
import { ForgetPass } from './features/auth/forget-pass/forget-pass';
import { VerCode } from './features/auth/ver-code/ver-code';
import { ResetPass } from './features/auth/reset-pass/reset-pass';

// Import Global Pages
import { Layout } from './layout/layout/layout';
import { Home } from './features/home/home';
import { NotFound } from './features/not-found/not-found';
import { Employers } from './features/employers/employers';
import { ContactUs } from './features/contact-us/contact-us';
import { CandidateProfile } from './features/candidate-profile/candidate-profile';

// ============ Routes ============
export const routes: Routes = [
  { path: '', redirectTo: 'home', pathMatch: 'full' },
  { path:'contact-us' , component :ContactUs},
  { path:'candidate-profile', component :CandidateProfile},
  // Public pages In Layout Component
  {
    path: '',
    component: Layout,
    children: [
      { path: 'home', component: Home },
      // { path: 'blog', component: Blog },
      // { path: 'contact', component: Contact },
      { path: 'employers', component: Employers },
      // { path: 'candidates', component: Candidates },
      // {
      // path: 'jobs',
      // component: Jobs
      // children: []
      // },
    ]
  },

  // Auth Pages
  { path: 'register', component: Register,canActivate: [guestGuard] },
  { path: 'login', component: Login,canActivate: [guestGuard] },
  { path: 'forget-pass', component: ForgetPass,canActivate: [guestGuard] },
  { path: 'ver-code', component: VerCode,canActivate: [guestGuard] },
  { path: 'reset-pass', component: ResetPass,canActivate: [guestGuard] },

  // 404 Page
  { path: '**', component: NotFound }
];
