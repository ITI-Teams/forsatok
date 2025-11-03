import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';

@Component({
  selector: 'app-reset-pass',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './reset-pass.html',
  styleUrl: './reset-pass.css',
})
export class ResetPass {

  newPassword: string = '';
  confirmPassword: string = '';
  showNewPassword: boolean = false;
  showConfirmPassword: boolean = false;
  passwordMismatch: boolean = false;
  passwordStrength: 'weak' | 'medium' | 'strong' | '' = '';
  
  passwordRequirements = {
    minLength: false,
    uppercase: false,
    lowercase: false,
    number: false
  };

  constructor(private router: Router) {}

  toggleNewPassword() {
    this.showNewPassword = !this.showNewPassword;
  }

  toggleConfirmPassword() {
    this.showConfirmPassword = !this.showConfirmPassword;
  }

  validatePassword() {
    const password = this.newPassword;
    
    // Check password requirements
    this.passwordRequirements.minLength = password.length >= 8;
    this.passwordRequirements.uppercase = /[A-Z]/.test(password);
    this.passwordRequirements.lowercase = /[a-z]/.test(password);
    this.passwordRequirements.number = /[0-9]/.test(password);

    // Calculate password strength
    const requirementsMet = Object.values(this.passwordRequirements).filter(Boolean).length;
    
    if (requirementsMet === 4) {
      this.passwordStrength = 'strong';
    } else if (requirementsMet >= 2) {
      this.passwordStrength = 'medium';
    } else if (password.length > 0) {
      this.passwordStrength = 'weak';
    } else {
      this.passwordStrength = '';
    }

    // Check password match if confirm password is filled
    if (this.confirmPassword) {
      this.checkPasswordMatch();
    }
  }

  checkPasswordMatch() {
    this.passwordMismatch = this.confirmPassword !== this.newPassword && this.confirmPassword.length > 0;
  }

  isFormValid(): boolean {
    const allRequirementsMet = Object.values(this.passwordRequirements).every(req => req);
    const passwordsMatch = this.newPassword === this.confirmPassword && this.confirmPassword.length > 0;
    
    return allRequirementsMet && passwordsMatch;
  }

  onSubmit() {
    if (!this.isFormValid()) {
      alert('Please ensure all password requirements are met and passwords match');
      return;
    }

    console.log('Resetting password...');
    
    // Handle password reset logic here
    // Example: this.authService.resetPassword(this.newPassword).subscribe(...)
    
    // After successful reset, navigate to login page
    // this.router.navigate(['/login']);
    
    alert('Password reset successfully! Please login with your new password.');
    this.router.navigate(['/login']);
  }
}
