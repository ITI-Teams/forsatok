// candidate-profile.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

interface Candidate {
  name: string;
  email: string;
  password: string;
  phone: string;
  education: string;
  experience: string;
  bio: string;
  resume: string | File;
}

@Component({
  selector: 'app-candidate-profile',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './candidate-profile.html',
  styleUrls: ['./candidate-profile.css']
})
export class CandidateProfile implements OnInit {
  
  candidate: Candidate = {
    name: 'Ahmed Mohamed',
    email: 'ahmed.mohamed@example.com',
    password: '********',
    phone: '+20 123 456 7890',
    education: `Bachelor of Computer Science
    Cairo University - 2018-2022
    GPA: 3.8/4.0

    Relevant Coursework:
    - Data Structures & Algorithms
    - Web Development
    - Database Management
    - Software Engineering`,
        experience: `Senior Frontend Developer
    Tech Solutions Inc. | 2022 - Present
    - Developed and maintained multiple Angular applications
    - Led a team of 3 junior developers
    - Improved application performance by 40%

    Junior Developer
    StartUp XYZ | 2021 - 2022
    - Built responsive web applications using Angular
    - Collaborated with design team for UI/UX improvements`,
    bio: 'Passionate full-stack developer with 3+ years of experience in building scalable web applications. Specialized in Angular, TypeScript, and modern web technologies. Always eager to learn new technologies and solve complex problems.',
    resume: ''
  };

  editCandidate: Candidate = { ...this.candidate };
  showModal: boolean = false;

  ngOnInit(): void {

  }

  openEditModal(): void {
    this.editCandidate = { ...this.candidate };
    this.showModal = true;
  }

  closeModal(): void {
    this.showModal = false;
    this.editCandidate = { ...this.candidate };
  }

  saveChanges(): void {
    this.candidate = { ...this.editCandidate };
    
    this.closeModal();
    
    console.log('Profile updated successfully!', this.candidate);
  }

  getInitials(): string {
    const names = this.candidate.name.split(' ');
    if (names.length >= 2) {
      return (names[0][0] + names[1][0]).toUpperCase();
    }
    return this.candidate.name.substring(0, 2).toUpperCase();
  }

    onFileSelected(event: any): void {
      const file = event.target.files[0];
      if (file && file.type === 'application/pdf') {
        this.editCandidate.resume = file;
      } else {
        alert('Please upload a valid PDF file.');
      }
    }



  viewResume() {
    if (this.candidate.resume) {
      if (typeof this.candidate.resume === 'string') {
        window.open(this.candidate.resume, '_blank');
      } else if (this.candidate.resume instanceof File) {
        const fileUrl = URL.createObjectURL(this.candidate.resume);
        window.open(fileUrl, '_blank');
      }
    }
  }


  getResumeName(): string {
    if (this.candidate.resume instanceof File) {
      return this.candidate.resume.name;
    } else if (typeof this.candidate.resume === 'string' && this.candidate.resume !== '') {
      return this.candidate.resume.split('/').pop() || 'Resume.pdf';
    }
    return 'No Resume Uploaded';
  }

  isResumeFile(): boolean {
    return this.candidate.resume instanceof File;
  }


}