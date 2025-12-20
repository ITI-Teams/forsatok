import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../core/services/auth.service';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, ToastModule],
  templateUrl: './register.html',
  styleUrls: ['./register.css'],
})
export class Register {
  name = '';
  email = '';
  password = '';
  agreeToTerms = false;
  showPassword = false;
  loading = false;

  constructor(
    private router: Router,
    private authService: AuthService,
    private messageService: MessageService
  ) { }

  showToast(severity: string, summary: string, detail: string) {
    this.messageService.add({ severity, summary, detail, life: 2500 });
  }

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  onSubmit() {
    if (!this.agreeToTerms) {
      this.showToast('warn', 'Terms', 'You must agree to the Terms & Conditions');
      return;
    }

    if (!this.name || !this.email || !this.password) {
      this.showToast('error', 'Error', 'Please fill all fields');
      return;
    }

    this.loading = true;
    const data = { name: this.name, email: this.email, password: this.password };

    this.authService.register(data).subscribe({
      next: (res: any) => {
        this.loading = false;
        this.showToast('success', 'Success', 'Account created successfully!');
        this.router.navigate(['/home']);
      },
      error: (err) => {
        this.loading = false;
        const errorMessage = this.authService.getErrorMessage(err);
        this.showToast('error', 'Error', errorMessage || 'Registration failed, please try again.');
      },
    });
  }


  continueWithLinkedIn() {
    this.authService.loginWithLinkedIn();
  }

  continueWithGoogle() {
    this.authService.loginWithGoogle();
  }
}
