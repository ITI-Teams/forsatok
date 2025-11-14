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
    bio?: string;
    experience?: string;
    education?: string;
    user?: {
      id: number;
      name: string;
      avatar: string;
    };
    location?: {
      city?: {
        name: string;
      };
      country?: {
        name: string;
      };
    };
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



  getCandidateName(candidate: any): string {
    return candidate.user?.name || candidate.name || 'Unknown Candidate';
  }

  getCandidateTitle(candidate: any): string {
    return candidate?.job_title || candidate.job_title || 'Unknown Job Title';
  }

  getCandidateLocation(candidate: any): string {
    if (candidate.location?.city?.name && candidate.location?.country?.name) {
      return `${candidate.location.city.name}, ${candidate.location.country.name}`;
    } else if (candidate.location?.city?.name) {
      return candidate.location.city.name;
    } else if (candidate.location?.country?.name) {
      return candidate.location.country.name;
    } else if (candidate.location) {
      return candidate.location;
    }
    return 'Location not specified';
  }

  getCandidateImage(candidate: any): string {
    if (!candidate.user?.avatar) {
      return '/images/avatars/avatar.svg';
    }

    if (candidate.user.avatar.startsWith('http')) {
      return candidate.user.avatar;
    }

    return `http://localhost:8000/storage/${candidate.user?.avatar}`;
  }

}
