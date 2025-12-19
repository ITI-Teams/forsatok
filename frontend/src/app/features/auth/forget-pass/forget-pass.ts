import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';

import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-forget-pass',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './forget-pass.html',
  styleUrl: './forget-pass.css',
})
export class ForgetPass {

   email: string = '';
   loading: boolean = false;

  constructor(
    private router: Router,
    private authService: AuthService
  ) {}

  onSubmit() {
    if (!this.email) {
      alert('Please enter your email address');
      return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(this.email)) {
      alert('Please enter a valid email address');
      return;
    }

    this.loading = true;
    this.authService.forgotPassword(this.email).subscribe({
      next: (res: any) => {
        this.loading = false;
        alert(res.message || 'Verification link sent! Please check your email.');
        // Optionally navigate to a page explaining that a link was sent
      },
      error: (err) => {
        this.loading = false;
        alert(this.authService.getErrorMessage(err));
      }
    });
  }
}
