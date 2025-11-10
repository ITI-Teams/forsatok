import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { DomSanitizer,SafeResourceUrl } from '@angular/platform-browser';

@Component({
  selector: 'app-job-details',
  imports: [CommonModule],
  templateUrl: './job-details.html',
  styleUrls: ['./job-details.css'],
})


export class JobDetails {
  job = {
    title: 'ACB Product Sales Specialist',
    company: 'Tech Innovators Inc.',
    company_logo: 'https://via.placeholder.com/100x100.png?text=Logo',
    location: 'Cairo, Egypt',
    experience: '3+ Years',
    description: `
      We are looking for a highly motivated Sales Specialist responsible for driving
      the growth of our ACB Product line. The ideal candidate will have a strong
      background in B2B sales, excellent communication skills, and a passion for technology.
    `,
    responsibilities: [
      'Develop and implement sales strategies to increase product awareness.',
      'Identify and target new business opportunities in the region.',
      'Collaborate with the marketing team to improve brand positioning.',
      'Provide after-sales support and build long-term client relationships.',
    ],
    qualifications: [
      'Bachelor’s degree in Business, Marketing, or related field.',
      'Proven track record of sales success in the tech industry.',
      'Strong negotiation and presentation skills.',
      'Fluent in English and Arabic.',
    ],
    salary_min: 10000,
    salary_max: 15000,
    type: 'Full-time',
    posted_date: '2025-11-01',
    deadline: '2025-12-31',
    company_description: `
      Tech Innovators Inc. is a leading technology company focused on delivering
      innovative solutions in the fields of AI, automation, and digital transformation.
    `,
    company_website: 'https://techinnovators.com',
    map_embed:
      'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.8438162801475!2d31.233334!3d30.044420!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145840c66e1f01d1%3A0xa4e2efdb5b95a1e!2sCairo%2C%20Egypt!5e0!3m2!1sen!2seg!4v1707481894213',
  };

  safeMapUrl!: SafeResourceUrl;

  similarJobs = [
    {
      title: 'Frontend Developer',
      company: 'SoftX Solutions',
      location: 'Alexandria, Egypt',
      salary: '10,000 - 14,000 EGP',
      type: 'Full-time',
    },
    {
      title: 'UI/UX Designer',
      company: 'Creative Studio',
      location: 'Cairo, Egypt',
      salary: '8,000 - 12,000 EGP',
      type: 'Remote',
    },
    {
      title: 'Backend Engineer',
      company: 'Nova Systems',
      location: 'Giza, Egypt',
      salary: '12,000 - 16,000 EGP',
      type: 'Full-time',
    },
  ];

  constructor(private sanitizer: DomSanitizer) {
    this.safeMapUrl = this.sanitizer.bypassSecurityTrustResourceUrl(this.job.map_embed);
  }
}
