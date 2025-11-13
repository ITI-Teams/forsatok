import { Component, Input, OnInit, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
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
export class FeaturedCandidates implements OnInit{
  @Input() candidates: Array<{ id?: number; name?: string; title?: string; location?: string; image?: string }> = [];

  ngOnInit() {
    import('swiper/element/bundle').then(({ register }) => register());
  }

}
