import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './register.html',
  styleUrls: ['./register.css'],
})

export class Register {

  firstName: string = '';
  lastName: string = '';
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

    if (!this.firstName || !this.lastName || !this.email || !this.password) {
      alert('Please fill all fields');
      return;
    }

    console.log('Registration data:', {
      firstName: this.firstName,
      lastName: this.lastName,
      email: this.email,
      password: this.password
    });

    
  }


  continueWithLinkedIn() {
    console.log('Continue with LinkedIn');
    
  }
}
