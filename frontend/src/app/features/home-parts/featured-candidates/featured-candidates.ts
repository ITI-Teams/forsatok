import { Component, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-featured-candidates',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './featured-candidates.html',
  styleUrls: ['./featured-candidates.css'],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
})
export class FeaturedCandidates {
  candidates = [
    { id: 1, name: 'Darlene Robertson', title: 'UI Designer', location: 'London, UK', image: '' },
    { id: 2, name: 'Floyd Miles', title: 'Chartered Accountant', location: 'London, UK', image: '' },
    { id: 3, name: 'Wade Warren', title: 'Developer', location: 'London, UK', image: '' },
    { id: 4, name: 'Floyd Miles', title: 'Chartered Accountant', location: 'London, UK', image: '' },
    { id: 5, name: 'Leslie Alexander', title: 'Marketing Expert', location: 'London, UK', image: '' },
    { id: 6, name: 'Floyd Miles', title: 'Chartered Accountant', location: 'London, UK', image: '' },
    { id: 8, name: 'Darlene Robertson', title: 'UI Designer', location: 'London, UK', image: '' },
    { id: 9, name: 'Wade Warren', title: 'Developer', location: 'London, UK', image: '' },
  ];

  ngOnInit() {
    import('swiper/element/bundle').then(({ register }) => register());
  }
}
