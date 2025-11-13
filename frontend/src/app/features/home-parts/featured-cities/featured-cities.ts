import { Component , Input } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-featured-cities',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './featured-cities.html',
  styleUrls: ['./featured-cities.css'],
})
export class FeaturedCities {
  @Input() cities: Array<{ id?: number; name?: string; jobs_count?: number; image?: string }> = [];
  
}
