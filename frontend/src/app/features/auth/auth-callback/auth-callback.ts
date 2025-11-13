import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-auth-callback',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './auth-callback.html',
  styleUrl: './auth-callback.css',
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
