import { Component , Input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-featured-cities',
  standalone: true,
  imports: [RouterLink,CommonModule],
  templateUrl: './featured-cities.html',
  styleUrls: ['./featured-cities.css'],
})
export class FeaturedCities {
  @Input() cities: Array<{ id?: number; name?: string; jobs_count?: number; image?: string }> = [];

  getFirstCity() {
    return this.cities && this.cities.length > 0 ? this.cities[0] : null;
  }

  getOtherCities() {
    return this.cities && this.cities.length > 1 ? this.cities.slice(1, 5) : [];
  }

  getCitySlug(city: any): string {
    if (!city || !city.name) return '';
    return city.name.toLowerCase().replace(/\s+/g, '-');
  }

  getCityImage(city: any): string {
    return city?.image || '/images/default-city.png';
  }

  getCityName(city: any): string {
    return city?.name || 'City Name';
  }

  getJobsCount(city: any): number {
    return city?.jobs_count || 0;
  }

}
