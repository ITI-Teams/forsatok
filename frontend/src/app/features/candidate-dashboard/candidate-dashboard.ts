import { NgClass, NgFor, NgIf } from '@angular/common';
import { Component } from '@angular/core';

@Component({
  selector: 'app-candidate-dashboard',
  standalone: true,
  imports: [NgClass, NgFor, NgIf],
  templateUrl: './candidate-dashboard.html'
})
export class CandidateDashboard {
  activeTab = 'description';

  candidateData = {
    description: {
      name: 'Hagar Elhalfawy',
      email: 'hagar@example.com',
      bio: 'Full Stack Developer with experience in Laravel, PHP, Angular, and MySQL.',
    },
    messages: [
      { from: 'Recruiter A', text: 'Please review your application.' },
      { from: 'HR B', text: 'Interview scheduled for next week.' }
    ],
    applications: [
      { jobTitle: 'Frontend Developer', status: 'Pending' },
      { jobTitle: 'Backend Developer', status: 'Accepted' }
    ],
    notifications: [
      { text: 'Your profile has been viewed 10 times today.' },
      { text: 'New job matching your skills has been posted.' }
    ],
    saved: [
      { jobTitle: 'Angular Developer' },
      { jobTitle: 'Full Stack Engineer' }
    ],
    reviews: [
      { reviewer: 'Manager X', comment: 'Great performance!' },
      { reviewer: 'Team Lead Y', comment: 'Excellent teamwork.' }
    ]
  };

  setTab(tab: string) {
    this.activeTab = tab;
  }
}
