import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Candidate, CandidateService } from '../../core/services/candidate.service';
import { HttpClientModule } from '@angular/common/http';
import { NgSelectModule } from '@ng-select/ng-select';


@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule ,NgSelectModule ,ReactiveFormsModule],
  templateUrl: './profile.html',
  styleUrls: ['./profile.css'],
})
export class Profile implements OnInit {

  profileForm!: FormGroup;
  candidate!: Candidate;
  resumeFile?: File | null;
  allSkills: { id: number; name: string }[] = [];

  constructor(
    private fb: FormBuilder,
    private candidateService: CandidateService
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.loadProfile();
    this.loadAllSkills();
  }

  private initForm(): void {
    this.profileForm = this.fb.group({
      name: [''],
      email: [''],
      password: [''],
      phone: [''],
      education: [''],
      experience: [''],
      bio: [''],
      gender: [''],
      dateOfBirth: [''],
      resume: [null],
      skills: [[]]
    });
  }

  loadProfile(): void {
    this.candidateService.getMyProfile().subscribe({
      next: (data: Candidate) => {
        this.candidate = data;
        this.profileForm.patchValue({
          name: data.name,
          email: data.email,
          phone: data.phone,
          education: data.education,
          experience: data.experience,
          bio: data.bio,
          gender: data.gender,
          dateOfBirth: data.dateOfBirth,
          skills: data.skills?.map(s => s.id) ?? []
        });
      },
      error: (err) => console.error('Error loading profile', err)
    });
  }

  onResumeChange(event: any): void {
    const file = event.target.files[0];
    if (file) this.resumeFile = file;
  }

  onSubmit(): void {
    const formData = new FormData();
    const formValue = this.profileForm.value;

    Object.keys(formValue).forEach(key => {
      if (key === 'resume' && this.resumeFile) {
        formData.append('resume', this.resumeFile);
      } else if (key === 'skills') {
        formData.append('skills', JSON.stringify(formValue.skills));
      } else if (formValue[key] !== null && formValue[key] !== '') {
        formData.append(key, formValue[key]);
      }
    });

    this.candidateService.updateProfile(formData).subscribe({
      next: (res) => {
        console.log('Profile updated', res);
        this.loadProfile();
      },
      error: (err) => console.error('Error updating profile', err)
    });
  }
  
  loadAllSkills(): void {
    // Skills API endpoint
    this.candidateService.getAllSkills().subscribe({
      next: (skills) => {
        this.allSkills = skills.map(s => ({ id: s.id, name: s.name }));
      },
      error: (err) => console.error('Error loading skills', err)
    });
  }
}