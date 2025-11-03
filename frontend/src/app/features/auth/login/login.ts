import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './login.html',
  styleUrls: ['./login.css'],
})
export class Login {

  email: string = '';
  password: string = '';
  agreeToTerms: boolean = false;
  showPassword: boolean = false;

  constructor(private router: Router) {}

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  onSubmit() {
    if (!this.agreeToTerms) {
      alert('Please agree to the Terms & Condition');
      return;
    }

    if (!this.email || !this.password) {
      alert('Please fill all fields');
      return;
    }

    console.log('Login data:', {
      email: this.email,
      password: this.password
    });

  
  }


  continueWithLinkedIn() {
    console.log('Continue with LinkedIn');
    
  }
}
