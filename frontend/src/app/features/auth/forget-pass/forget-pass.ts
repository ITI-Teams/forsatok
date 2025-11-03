import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';

@Component({
  selector: 'app-forget-pass',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './forget-pass.html',
  styleUrl: './forget-pass.css',
})
export class ForgetPass {

   email: string = '';

  constructor(private router: Router) {}

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

    console.log('Sending verification code to:', this.email);

    // Handle forgot password logic here
    // For example: send verification code via API
    // this.authService.sendPasswordResetCode(this.email).subscribe(...)
    
    // After successful request, you might want to navigate to verification page
    // this.router.navigate(['/verify-code'], { queryParams: { email: this.email } });
    
    alert('Verification code sent! Please check your email.');
  }
}
