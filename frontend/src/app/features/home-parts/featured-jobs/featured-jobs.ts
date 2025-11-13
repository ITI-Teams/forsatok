import { Component , Input} from '@angular/core';

@Component({
  selector: 'app-featured-jobs',
  imports: [],
  templateUrl: './featured-jobs.html',
  styleUrl: './featured-jobs.css',
})
export class FeaturedJobs {
   @Input() jobs: Array<{
    id?: number;
    title?: string;
    company?: string;
    location?: string;
    salary_min?: number;
    salary_max?: number;
    type?: string;
    deadline?: string;
  }> = [];

}
