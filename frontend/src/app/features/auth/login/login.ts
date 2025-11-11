import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';


@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule,ToastModule],
  templateUrl: './login.html',
  styleUrls: ['./login.css'],
})
export class Login {
  form = { email: '', password: '' };
  showPassword = false;
  loading = false;
  agreeToTerms: boolean = false;

  constructor(
    private router: Router,
    private auth: AuthService,
    private messageService: MessageService
  ) {}

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  showToast(severity: string, summary: string, detail: string) {
    this.messageService.add({ severity, summary, detail, life: 2500 });
  }

  onSubmit() {
    if (!this.form.email || !this.form.password) {
      this.showToast('warn', 'Missing fields', 'Please enter your email and password.');
      return;
    }
    if (!this.agreeToTerms) {
      this.showToast('warn', 'Terms & Condition', 'Please agree to the Terms & Condition');
      return;
    }

    this.loading = true;

    this.auth.login(this.form).subscribe({
      next: (res: any) => {
        this.loading = false;
        this.showToast('success', 'Welcome!', 'Login successful.');
      },
      error: (err) => {
        this.loading = false;
        const errorMessage = this.auth.getErrorMessage(err);
        
        if (err.status === 401) {
          this.showToast('error', 'Unauthorized', errorMessage || 'Invalid email or password.');
        } else if (err.status === 0) {
          this.showToast('error', 'Network Error', 'Cannot reach the server.');
        } else {
          this.showToast('error', 'Error', errorMessage || 'Something went wrong, please try again.');
        }
      },
    });
  }

  continueWithLinkedIn() {
    this.auth.loginWithLinkedIn();
  }
}