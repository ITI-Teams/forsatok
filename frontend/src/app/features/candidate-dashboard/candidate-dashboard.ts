import { NgClass, NgFor, NgIf } from '@angular/common';
import { Component } from '@angular/core';
// import {ContactService} from '../../core/services/main-contact.service';

@Component({
  selector: 'app-candidate-dashboard',
  standalone: true,
  imports: [NgClass, NgFor, NgIf, ],
  templateUrl: './candidate-dashboard.html'
})
export class CandidateDashboard {
  activeTab = 'description';

  candidateData = {
    messages: [
      { from: 'Recruiter A', text: 'Please review your application.' },
      { from: 'HR B', text: 'Interview scheduled for next week.' }
    ],
    
    notifications: [
      { text: 'Your profile has been viewed 10 times today.' },
      { text: 'New job matching your skills has been posted.' }
    ],
    
    saved: [
      { jobTitle: 'Angular Developer' },
      { jobTitle: 'Full Stack Engineer' }
    ],
  };

  setTab(tab: string) {
    this.activeTab = tab;
  }
}
