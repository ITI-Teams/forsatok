import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { guestGuard } from './core/guards/guest.guard';

// Import Auth Pages
import { Register } from './features/auth/register/register';
import { Login } from './features/auth/login/login';
import { ForgetPass } from './features/auth/forget-pass/forget-pass';
import { VerCode } from './features/auth/ver-code/ver-code';
import { ResetPass } from './features/auth/reset-pass/reset-pass';
import { AuthCallback } from './features/auth/auth-callback/auth-callback';

// Import Global Pages
import { Layout } from './layout/layout/layout';
import { Home } from './features/home/home';
import { NotFound } from './features/not-found/not-found';
import { ContactUs } from './features/contact-us/contact-us';
import { CandidateProfile } from './features/candidate-profile/candidate-profile';
import { Company } from './features/company/company';
import { CompaniesList } from './features/companies-list/companies-list';
import { Candidates } from './features/candidates/candidates';
import {Jobs} from './features/jobs/jobs';
import { JobDetails } from './features/job-details/job-details';
import { Profile } from './features/profile/profile';
import {JobApplication} from './features/job-application/job-application';

// ============ Routes ============
export const routes: Routes = [
  { path: '', redirectTo: 'home', pathMatch: 'full' },
  // Public pages In Layout Component
  {
    path: '',
    component: Layout,
    children: [
      { path: 'home', component: Home ,canActivate: [guestGuard]},
      { path:'jobs', component :Jobs,canActivate: [guestGuard]},
      { path: 'job/:id', component: JobDetails ,canActivate: [guestGuard]},
      { path: 'candidates', component: Candidates ,canActivate: [guestGuard]},
      { path: 'candidate/:id', component: CandidateProfile ,canActivate: [guestGuard]},
      { path: 'contact', component: ContactUs ,canActivate: [guestGuard] },
      { path: 'profile', component :Profile ,canActivate: [authGuard]},
      { path: 'company/:id',component:Company ,canActivate: [guestGuard]},
      { path: 'company',component:CompaniesList ,canActivate: [guestGuard]},
      { path: 'apply/:id', component: JobApplication,canActivate: [authGuard] },
      { path: 'apply', component: JobApplication, canActivate: [authGuard]},

    ]
  },
  // Auth Pages
  { path: 'register', component: Register,canActivate: [guestGuard] },
  { path: 'login', component: Login,canActivate: [guestGuard] },
  { path: 'forget-pass', component: ForgetPass,canActivate: [guestGuard] },
  { path: 'ver-code', component: VerCode,canActivate: [guestGuard] },
  { path: 'reset-pass', component: ResetPass,canActivate: [guestGuard] },
  { path: 'auth/callback', component: AuthCallback,canActivate: [guestGuard] },

  // 404 Page
  { path: '**', component: NotFound }
];
