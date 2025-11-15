import { NgClass, NgFor, NgIf } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { HomeService } from '../../core/services/home.service';
import { ContactService } from '../../core/services/main-contact.service';

@Component({
  selector: 'app-candidate-dashboard',
  standalone: true,
  imports: [NgClass, NgFor, NgIf],
  templateUrl: './candidate-dashboard.html'
})
export class CandidateDashboard implements OnInit {
  activeTab: string = 'messages';

  candidateData: any = {
    messages: [],
    // notifications: [],
    savedJobs: []
  };

  currentPageMessages: number = 1;
  currentPageSaved: number = 1;
  itemsPerPage: number = 5;

  constructor(private homeService: HomeService, private contactService: ContactService) { }

  ngOnInit() {
    this.loadMessages();
  }

  setTab(tab: string) {
    this.activeTab = tab;

    if (tab === 'messages') {
      this.loadMessages();
    } else if (tab === 'savedJobs') {
      this.loadSavedJobs();
    }

  }
  loadMessages() {
    this.contactService.getMessages().subscribe(
      (res: any) => {
        this.candidateData.messages = res.data || [];
        this.currentPageMessages = 1;
      },
      (error) => {
        console.error('Error fetching messages:', error);
      }
    );
  }


  loadSavedJobs() {
    this.homeService.getHomeData().subscribe(
      (data: any) => {
        this.candidateData.savedJobs = data.savedJobs || [];
        this.currentPageSaved = 1;
      },
      (error) => {
        console.error('Error fetching saved jobs:', error);
      }
    );
  }

  get paginationMessages() {
    const start = (this.currentPageMessages - 1) * this.itemsPerPage;
    return this.candidateData.messages.slice(start, start + this.itemsPerPage);
  }

  get paginationSavedJobs() {
    const start = (this.currentPageSaved - 1) * this.itemsPerPage;
    return this.candidateData.savedJobs.slice(start, start + this.itemsPerPage);
  }

  nextPage(tab: string) {
    if(tab === 'messages' && this.currentPageMessages < this.totalPages('messages')) this.currentPageMessages++;
    else if(tab === 'savedJobs' && this.currentPageSaved < this.totalPages('savedJobs')) this.currentPageSaved++;
  }

  prevPage(tab: string) {
    if(tab === 'messages' && this.currentPageMessages > 1) this.currentPageMessages--;
    else if(tab === 'savedJobs' && this.currentPageSaved > 1) this.currentPageSaved--;
  }

  totalPages(tab: string) {
    if(tab === 'messages') return Math.ceil(this.candidateData.messages.length / this.itemsPerPage);
    if(tab === 'savedJobs') return Math.ceil(this.candidateData.savedJobs.length / this.itemsPerPage);
    return 1;
  }

  
}
