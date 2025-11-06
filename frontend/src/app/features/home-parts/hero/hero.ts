import { CommonModule } from '@angular/common';
import { Component, ViewChild, ElementRef, AfterViewInit, Renderer2 } from '@angular/core';

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
  imports: [CommonModule],
  templateUrl: './hero.html',
  styleUrls: ['./hero.css'],
})
export class Hero implements AfterViewInit {
  @ViewChild('heroVideo', { static: false }) heroVideo!: ElementRef<HTMLVideoElement>;

  constructor(private renderer: Renderer2) { }

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

    // video.addEventListener('error', () => {
    //   console.warn('Video failed to load:', video.error);
    //   const container = video.closest('.relative');
    //   if (container) {
    //     this.renderer.addClass(container, 'bg-gray-100');
    //     this.renderer.addClass(container, 'dark:bg-gray-900');
    //   }
    // });

    const playPromise = video.play();
    if (playPromise !== undefined) {
      playPromise.catch((error) => {
        console.warn('Video autoplay prevented:', error);
        video.muted = true;
        video.play().catch(() => console.warn('Video play failed even after muting'));
      });
    }
  }
}
