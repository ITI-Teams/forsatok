import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ContactService } from '../../core/services/main-contact.service';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';

@Component({
  selector: 'app-contact-us',
  standalone: true,
  imports: [CommonModule, FormsModule, ToastModule],
  providers: [MessageService],
  templateUrl: './contact-us.html',
  styleUrl: './contact-us.css',
})
export class ContactUs {
  constructor(
    private contactService: ContactService,
    private messageService: MessageService
  ) {}

  formData = {
    fullName: '',
    email: '',
    subject: '',
    message: ''
  };

  socialLinks = [
    { name: 'Facebook', url: '#' },
    { name: 'Instagram', url: '#' },
    { name: 'YouTube', url: '#' },
    { name: 'TikTok', url: '#' }
  ];

  partners = [
    'Spherule',
    'Luminous',
    'FocalPoint',
    'Polymath',
    'Acme Corp',
    'CloudWatch'
  ];

  sendMessage() {
    if (!this.formData.fullName || !this.formData.email || !this.formData.message) {
      this.messageService.add({
        severity: 'warn',
        summary: 'Missing Fields',
        detail: 'Please fill in all required fields'
      });
      return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(this.formData.email)) {
      this.messageService.add({
        severity: 'error',
        summary: 'Invalid Email',
        detail: 'Please enter a valid email address'
      });
      return;
    }

    this.contactService.sendMessage(this.formData).subscribe({
      next: () => {
        this.messageService.add({
          severity: 'success',
          summary: 'Message Sent',
          detail: 'Thank you for contacting us!'
        });
        this.resetForm();
      },
      error: () => {
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to send message'
        });
      }
    });
  }


  resetForm() {
    this.formData = {
      fullName: '',
      email: '',
      subject: '',
      message: ''
    };
  }
}
