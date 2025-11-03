import { CommonModule } from '@angular/common';
import { Component, ElementRef, OnDestroy, OnInit, QueryList, ViewChildren } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';

@Component({
  selector: 'app-ver-code',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './ver-code.html',
  styleUrl: './ver-code.css',
})
export class VerCode implements OnInit, OnDestroy {

  @ViewChildren('otpInput') otpInputs!: QueryList<ElementRef>;
  
  email: string = '...martinez@gmail.com';
  digits: string[] = ['', '', '', ''];
  timeLeft: number = 24; // seconds
  private timerInterval: any;

  constructor(
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    // Get email from query params if passed from previous page
    this.route.queryParams.subscribe(params => {
      if (params['email']) {
        this.email = this.maskEmail(params['email']);
      }
    });

    // Start countdown timer
    this.startTimer();
  }

  ngOnDestroy() {
    if (this.timerInterval) {
      clearInterval(this.timerInterval);
    }
  }

  startTimer() {
    this.timerInterval = setInterval(() => {
      if (this.timeLeft > 0) {
        this.timeLeft--;
      } else {
        clearInterval(this.timerInterval);
      }
    }, 1000);
  }

  formatTime(seconds: number): string {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  }

  maskEmail(email: string): string {
    const parts = email.split('@');
    if (parts.length !== 2) return email;
    
    const username = parts[0];
    const domain = parts[1];
    const maskedUsername = username.substring(0, 3) + '...';
    return `${maskedUsername}@${domain}`;
  }

  onDigitInput(event: Event, index: number) {
  const input = event.target as HTMLInputElement;
  let value = input.value;

  // ✅ لا يقبل إلا رقم واحد فقط
  if (!/^\d$/.test(value)) {
    this.digits[index] = '';
    input.value = '';
    return;
  }

  // ✅ تحديث المصفوفة بشكل صحيح
  this.digits[index] = value;

  // ✅ لو مش آخر خانة → روح على اللي بعدها
  if (index < this.digits.length - 1) {
    setTimeout(() => {
      this.otpInputs.toArray()[index + 1].nativeElement.focus();
    }, 60);
  }
}

onKeyDown(event: KeyboardEvent, index: number) {
  const input = event.target as HTMLInputElement;

  // ✅ لما يدوس Backspace → يرجع للخانة اللي قبلها
  if (event.key === 'Backspace') {
    if (input.value === '' && index > 0) {
      setTimeout(() => {
        this.otpInputs.toArray()[index - 1].nativeElement.focus();
      }, 50);
    }
    return;
  }

  // ✅ يمنع أي كتابة غير أرقام نهائيًا
  if (!/^\d$/.test(event.key)) {
    event.preventDefault();
  }
}


  isCodeComplete(): boolean {
    return this.digits.every(digit => digit !== '');
  }

  onSubmit() {
    if (!this.isCodeComplete()) {
      alert('Please enter the complete verification code');
      return;
    }

    const code = this.digits.join('');
    console.log('Verification code:', code);

    // Handle verification logic here
    // For example: verify code via API
    // this.authService.verifyCode(code).subscribe(...)
    
    // After successful verification, navigate to next page
    // this.router.navigate(['/reset-password']);
    
    alert('Code verified successfully!');
  }

  resendCode() {
    if (this.timeLeft > 0) return;

    console.log('Resending verification code...');
    
    // Handle resend code logic here
    // this.authService.resendCode(this.email).subscribe(...)
    
    // Reset timer
    this.timeLeft = 24;
    this.startTimer();
    
    // Clear inputs
    this.digits = ['', '', '', ''];
    
    alert('Verification code sent!');
  }
}
