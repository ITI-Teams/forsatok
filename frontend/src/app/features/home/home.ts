import { Component } from '@angular/core';
import { Hero } from '../home-parts/hero/hero';
import { HowItWork } from "../home-parts/how-it-work/how-it-work";
import { FeaturedJobs } from "../home-parts/featured-jobs/featured-jobs";
import { FeaturedCities } from "../home-parts/featured-cities/featured-cities";

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [Hero, HowItWork, FeaturedJobs, FeaturedCities],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home {

}
