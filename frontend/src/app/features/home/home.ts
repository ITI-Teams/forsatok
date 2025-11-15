import { Component, OnInit, inject } from '@angular/core';
import { Hero } from '../home-parts/hero/hero';
import { HowItWork } from "../home-parts/how-it-work/how-it-work";
import { FeaturedJobs } from "../home-parts/featured-jobs/featured-jobs";
import { FeaturedCities } from "../home-parts/featured-cities/featured-cities";
import { FindYourDreamJob } from "../home-parts/find-your-dream-job/find-your-dream-job";
import { FeaturedCandidates } from "../home-parts/featured-candidates/featured-candidates";
import { HomeService } from '../../core/services/home.service';
import { CommonModule } from '@angular/common';
// import { ToastService, Toast } from '../../core/services/toast.service';
@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, Hero, HowItWork, FeaturedJobs, FeaturedCities, FindYourDreamJob, FeaturedCandidates],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home implements OnInit {
  jobs: any[] = [];
  topCities: any[] = [];
  candidatesCarousel: any[] = [];
  loading = true;

  // protected toastService = inject(ToastService);
  constructor(private homeService: HomeService) {}

  ngOnInit() {
    this.homeService.getHomeData().subscribe({
      next: (data) => {
        this.jobs = data.jobs || [];
        this.topCities = data.top_cities || [];
        this.candidatesCarousel = data.candidates_carousel || [];
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading home data:', err);
        this.loading = false;
      },
    });
  }


  ngAfterViewInit() {
    // console.log('ToastService injected:', this.toastService);
  }
}
