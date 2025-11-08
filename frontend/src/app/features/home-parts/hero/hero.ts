import { CommonModule } from '@angular/common';
import { Component, ViewChild, ElementRef, AfterViewInit, Renderer2 } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

interface Candidate {
  initials: string;
}

interface CompanyIcon {
  faClass: string;
  colorClass: string;
  position: any;
}

@Component({
  selector: 'app-hero',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './hero.html',
  styleUrls: ['./hero.css'],
})
export class Hero implements AfterViewInit {
  searchTerm: string = '';
  selectedLocation: string = '';

  @ViewChild('heroVideo', { static: false }) heroVideo!: ElementRef<HTMLVideoElement>;

  constructor(private renderer: Renderer2, private router: Router) { }

  candidates: Candidate[] = Array.from({ length: 7 }, (_, i) => ({
    initials: `C${i + 1}`,
  }));

  companyIcons: CompanyIcon[] = [
    { faClass: 'fab fa-behance', colorClass: 'text-blue-600', position: { top: '-1rem', right: '-1rem' } },
    { faClass: 'fab fa-slack', colorClass: 'text-purple-600', position: { top: '5rem', left: '-3rem' } },
    { faClass: 'fab fa-linkedin-in', colorClass: 'text-blue-700', position: { bottom: '6rem', left: '-4rem' } },
    { faClass: 'fab fa-figma', colorClass: 'text-pink-500', position: { top: '8rem', right: '-4rem' } },
    { faClass: 'fab fa-twitter', colorClass: 'text-blue-400', position: { bottom: '8rem', right: '2rem' } },
    { faClass: 'fab fa-wordpress', colorClass: 'text-blue-900', position: { bottom: '-1rem', left: '8rem' } },
  ];

  ngAfterViewInit() {
    const video = this.heroVideo?.nativeElement;
    if (!video) return;

    video.muted = true;
    video.playsInline = true;
    video.autoplay = true;
    video.loop = true;

    const playPromise = video.play();
    if (playPromise !== undefined) {
      playPromise.catch(() => {
        console.warn('Autoplay blocked, trying again muted');
        video.muted = true;
        video.play().catch(() => console.warn('Video still cannot play'));
      });
    }
  }

  goToJobs() {
    const queryParams: any = {};

    if (this.searchTerm?.trim()) {
      queryParams.search = this.searchTerm.trim().toLowerCase();
    }

    if (this.selectedLocation?.trim()) {
      queryParams.location = this.selectedLocation.trim().toLowerCase();
    }

    this.router.navigate(['/jobs'], { queryParams });
  }
}
