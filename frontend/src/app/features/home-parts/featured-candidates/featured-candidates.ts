import { Component, Input, OnInit, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink,Router } from '@angular/router';
import {HomeService} from '../../../core/services/home.service';
import {ToastService} from '../../../core/services/toast.service';

@Component({
  selector: 'app-featured-candidates',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './featured-candidates.html',
  styleUrls: ['./featured-candidates.css'],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
})
export class FeaturedCandidates implements OnInit{
  @Input() candidates: Array<{
    id?: number;
    user_id?: number;
    name?: string;
    title?: string;
    location?: string;
    image?: string
  }> = [];


  constructor(
    private router: Router,
    private homeService: HomeService,
    private toastService: ToastService
  ) {}

  ngOnInit() {
    import('swiper/element/bundle').then(({ register }) => register());
  }

  viewCandidate(user_id: number | undefined) {
    if (user_id) {
      this.router.navigate(['/candidate', user_id]);
    } else {
      this.router.navigate(['/candidates']);
    }
  }

}
