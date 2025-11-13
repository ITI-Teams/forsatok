import { Component, OnInit } from '@angular/core';

import { CommonModule } from '@angular/common';

import { FormsModule } from '@angular/forms';

import { Candidate, CandidateService } from '../../core/services/candidate.service';

@Component({

  selector: 'app-profile',

  standalone: true,

  imports: [CommonModule, FormsModule],

  templateUrl: './profile.html',

  styleUrls: ['./profile.css'],

})

export class Profile implements OnInit {

  candidate: Candidate | null = null;

  editCandidate: Candidate | null = null;

  showModal = false;

  isLoading = false;

  constructor(private candidateService: CandidateService) {}

  ngOnInit(): void {

    this.loadCandidateProfile();

  }

  loadCandidateProfile(): void {

    this.isLoading = true;

    this.candidateService.getMyProfile().subscribe({

      next: (data) => {

        this.candidate = data;

        this.isLoading = false;

      },

      error: (err) => {

        console.error('Error loading profile', err);

        this.isLoading = false;

      }

    });

  }

  openEditModal(): void {

    if (this.candidate) {

      this.editCandidate = { ...this.candidate };

      this.showModal = true;

    }

  }

  closeModal(): void {

    this.showModal = false;

    this.editCandidate = null;

  }

  saveChanges(): void {

    if (!this.editCandidate) return;

    const formData = new FormData();

    Object.entries(this.editCandidate).forEach(([key, value]) => {

      if (value !== null && value !== undefined) {

        if (key === 'skills' && Array.isArray(value)) {

          formData.append(key, JSON.stringify(value));

        } else if (key === 'resume' && value instanceof File) {

          formData.append('resume', value);

        } else {

          formData.append(key, value as any);

        }

      }

    });

    this.candidateService.updateProfile(formData).subscribe({

      next: (res) => {

        console.log('Profile updated successfully!', res);

        this.candidate = this.editCandidate;

        this.closeModal();

      },

      error: (err) => {

        console.error('Error updating profile', err);

      }

    });

  }

  onFileSelected(event: any): void {

    const file = event.target.files[0];

    if (file && file.type === 'application/pdf') {

      if (this.editCandidate) this.editCandidate.resume = file;

    } else {

      alert('Please upload a valid PDF file.');

    }

  }

  viewResume(): void {

    const resume = this.editCandidate?.resume || this.candidate?.resume;

    if (!resume) return;

    if (typeof resume === 'string') window.open(resume, '_blank');

    else if (resume instanceof File) window.open(URL.createObjectURL(resume), '_blank');

  }

  getResumeName(): string {

    const resume = this.editCandidate?.resume || this.candidate?.resume;

    if (resume instanceof File) return resume.name;

    if (typeof resume === 'string' && resume !== '') return resume.split('/').pop() || 'Resume.pdf';

    return 'No Resume Uploaded';

  }

  getInitials(): string {

    const name = this.candidate?.name || '';

    const parts = name.split(' ');

    return parts.length >= 2 ? (parts[0][0] + parts[1][0]).toUpperCase() : name.substring(0, 2).toUpperCase();

  }

}

 