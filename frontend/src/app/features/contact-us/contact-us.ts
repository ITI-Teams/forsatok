import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-contact-us',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './contact-us.html',
  styleUrl: './contact-us.css',
})
export class ContactUs {

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
      alert('Please fill in all required fields!');
      return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(this.formData.email)) {
      alert('Please enter a valid email address!');
      return;
    }

    console.log('Form Data:', this.formData);
    
    alert('Message sent successfully! Thank you for contacting us.');
    this.resetForm();
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
