import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CheckboxModule } from 'primeng/checkbox';
import { SliderModule } from 'primeng/slider';
import { ButtonModule } from 'primeng/button';
import { CardModule } from 'primeng/card';
import { JobService, Job } from '../../core/services/job.service';

@Component({
  selector: 'app-jobs',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    CheckboxModule,
    SliderModule,
    ButtonModule,
    CardModule,
  ],
  templateUrl: './jobs.html',
  styleUrls: ['./jobs.css'],
})
export class Jobs implements OnInit {
  jobs: Job[] = [];
  filteredJobs: Job[] = [];
  paginatedJobs: Job[] = [];

  // 🔍 الفلاتر
  searchTerm = '';
  selectedLocation = '';
  selectedCategory = '';
  selectedLevel = '';
  selectedType = '';
  salaryRange: number[] = [1000, 10000];

  // 📍 القوائم
  locations = [
    { name: 'Cairo', value: 'cairo' },
    { name: 'Alexandria', value: 'alexandria' },
    { name: 'Giza', value: 'giza' },
  ];

  jobCategories = [
    { name: 'Web Development', value: 'web' },
    { name: 'Design', value: 'design' },
    { name: 'Marketing', value: 'marketing' },
    { name: 'Finance', value: 'finance' },
  ];

  employmentTypes = [
    { name: 'Full-time', value: 'full', selected: false, count: 12 },
    { name: 'Part-time', value: 'part', selected: false, count: 8 },
    { name: 'Remote', value: 'remote', selected: false, count: 5 },
    { name: 'Freelance', value: 'freelance', selected: false, count: 3 },
  ];

  jobLevels = [
    { name: 'Internship', value: 'intern', selected: false, count: 4 },
    { name: 'Junior', value: 'junior', selected: false, count: 9 },
    { name: 'Mid-Level', value: 'mid', selected: false, count: 7 },
    { name: 'Senior', value: 'senior', selected: false, count: 5 },
  ];

  // 🧭 Pagination
  currentPage = 1;
  itemsPerPage = 6;
  totalPages = 1;

  isFilterOpen = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private jobService: JobService
  ) {}

  ngOnInit() {
    this.jobService.getJobs().subscribe({
      next: (data) => {
        this.jobs = data;
        this.filteredJobs = [...this.jobs];
        this.updatePagination();
      },
      error: (err) => console.error('Error loading jobs:', err),
    });

    this.route.queryParams.subscribe((params) => {
      if (params['search']) {
        this.searchTerm = params['search'];
        this.applyFilters();
      }
    });
  }

  toggleFilters() {
    this.isFilterOpen = !this.isFilterOpen;
  }

  onSearchInput() {
    this.applyFilters();
  }

  selectQuickCategory(category: string) {
    this.selectedCategory = category;
    this.applyFilters();
  }

  applyFiltersAndClose() {
    this.applyFilters();
    this.isFilterOpen = false;
  }

  applyFilters() {
    this.filteredJobs = this.jobs.filter((job) => {
      const salaryOk =
        job.salary >= this.salaryRange[0] && job.salary <= this.salaryRange[1];

      const typeSelected = this.employmentTypes
        .filter((t) => t.selected)
        .map((t) => t.value);

      const levelSelected = this.jobLevels
        .filter((l) => l.selected)
        .map((l) => l.value);

      return (
        (!this.searchTerm ||
          (job.title ?? '').toLowerCase().includes(this.searchTerm.toLowerCase())) &&
        (!this.selectedCategory || (job.category ?? '') === this.selectedCategory) &&
        (!this.selectedLocation || (job.location ?? '') === this.selectedLocation) &&
        (typeSelected.length === 0 || typeSelected.includes(job.type ?? '')) &&
        (levelSelected.length === 0 || levelSelected.includes(job.level ?? '')) &&
        salaryOk
      );
    });

    this.updatePagination();
  }

  resetFilters() {
    this.searchTerm = '';
    this.selectedCategory = '';
    this.selectedLocation = '';
    this.salaryRange = [1000, 10000];
    this.employmentTypes.forEach((t) => (t.selected = false));
    this.jobLevels.forEach((l) => (l.selected = false));
    this.applyFilters();
  }

  updatePagination() {
    this.totalPages = Math.ceil(this.filteredJobs.length / this.itemsPerPage);
    this.paginate();
  }

  paginate() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    const end = start + this.itemsPerPage;
    this.paginatedJobs = this.filteredJobs.slice(start, end);
  }

  previousPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
      this.paginate();
    }
  }

  nextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
      this.paginate();
    }
  }

  viewJob(id: number) {
    this.router.navigate(['/job', id]);
  }
}
