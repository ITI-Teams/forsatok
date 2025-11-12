import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-auth-callback',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="callback-container">
      <div *ngIf="loading" class="loading">
        <div class="spinner"></div>
        <p>Processing LinkedIn login...</p>
      </div>
      <div *ngIf="error" class="error">
        <h3>Login Failed</h3>
        <p>{{ error }}</p>
        <button (click)="redirectToLogin()" class="btn btn-primary">Return to Login</button>
      </div>
    </div>
  `,
  styles: [`
    .callback-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
    }
    .loading {
      font-size: 18px;
    }
    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #0077b5;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 2s linear infinite;
      margin: 0 auto 20px;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .error {
      color: #d32f2f;
      max-width: 400px;
    }
  `]
})
export class AuthCallback implements OnInit {
  loading = true;
  error: string | null = null;

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit() {
    this.handleCallback();
  }

  handleCallback() {
    const result = this.authService.handleLinkedInCallback();
    
    if (result.success) {
      this.loading = false;
    } else {
      this.loading = false;
      this.error = result.error || 'Unknown error occurred during LinkedIn authentication';
    }
  }

  redirectToLogin() {
    this.router.navigate(['/login']);
  }
}