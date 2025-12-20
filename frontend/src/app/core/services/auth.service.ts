import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { tap, catchError } from 'rxjs/operators';
import { BehaviorSubject, throwError } from 'rxjs';
import { Router } from '@angular/router';
import {environment} from '../../environments/environment';
@Injectable({ providedIn: 'root' })
export class AuthService {
  // private apiUrl = 'https://balkiest-irretraceable-timika.ngrok-free.dev/api/auth';
  private apiUrl = `${environment.apiUrl}/auth`;
  private loggedIn = new BehaviorSubject<boolean>(this.hasToken());

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  register(data: any) {
    return this.http.post(`${this.apiUrl}/candidate/register`, data).pipe(
      tap((res: any) => {
        if (res.token) {
          localStorage.setItem('token', res.token);
          this.setUser(res.user);
          this.loggedIn.next(true);
        }
      }),
      catchError(error => {
        return throwError(() => error);
      })
    );
  }

  login(data: any) {
    return this.http.post(`${this.apiUrl}/candidate/login`, data).pipe(
      tap((res: any) => {
        if (res.token) {
          localStorage.setItem('token', res.token);
          this.setUser(res.user);
          this.loggedIn.next(true);
        }
      }),
      catchError(error => {
        return throwError(() => error);
      })
    );
  }

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.loggedIn.next(false);
  }

  forgotPassword(email: string) {
    return this.http.post(`${this.apiUrl}/candidate/forgot-password`, { email });
  }

  resetPassword(data: any) {
    return this.http.post(`${this.apiUrl}/candidate/reset-password`, data);
  }

  getToken() {
    return localStorage.getItem('token');
  }

  hasToken(): boolean {
    return !!localStorage.getItem('token');
  }

  private setToken(token: string): void {
    localStorage.setItem('token', token);
    this.loggedIn.next(true);
  }


  getUser(): any {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }

  private setUser(user: any): void {
    localStorage.setItem('user', JSON.stringify(user));
  }

  isLoggedIn$() {
    return this.loggedIn.asObservable();
  }

  loginWithLinkedIn() {
    window.location.href = `${this.apiUrl}/linkedin/redirect`;
  }

  loginWithGoogle() {
    // Universal Google OAuth redirect route
    const googleUrl = `${environment.apiUrl.replace('/api', '')}/auth/google?source=jobhub&type=candidate`;
    window.location.href = googleUrl;
  }

  handleExternalAuthCallback(): { success: boolean; token?: string; user?: any; error?: string } {
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    const userParam = urlParams.get('user');
    const error = urlParams.get('error');
    const message = urlParams.get('message');

    if (error) {
      const errorMessage = message || error;
      return { success: false, error: errorMessage };
    }

    if (token && userParam) {
      try {
        const user = JSON.parse(decodeURIComponent(userParam));
        this.setToken(token);
        this.setUser(user);

        this.clearUrlParams();

        this.router.navigate(['/home']);

        return { success: true, token, user };
      } catch (e) {
        return { success: false, error: 'Invalid user data' };
      }
    }

    return { success: false, error: 'No token received' };
  }

  private clearUrlParams(): void {
    window.history.replaceState({}, document.title, window.location.pathname);
  }

  getErrorMessage(error: any): string {
    if (error.error && error.error.message) {
      return error.error.message;
    } else if (error.error && typeof error.error === 'string') {
      return error.error;
    } else if (error.message) {
      return error.message;
    } else {
      return 'An unknown error occurred';
    }
  }
}
