import { Component } from '@angular/core';
import { Hero } from '../home-parts/hero/hero';
import { HowItWork } from "../home-parts/how-it-work/how-it-work";
import { FeaturedJobs } from "../home-parts/featured-jobs/featured-jobs";
import { FeaturedCities } from "../home-parts/featured-cities/featured-cities";
import { FindYourDreamJob } from "../home-parts/find-your-dream-job/find-your-dream-job";
import { FeaturedCandidates } from "../home-parts/featured-candidates/featured-candidates";

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [Hero, HowItWork, FeaturedJobs, FeaturedCities, FindYourDreamJob, FeaturedCandidates],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home {

}
