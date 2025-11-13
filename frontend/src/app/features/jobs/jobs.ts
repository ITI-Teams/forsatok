import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CheckboxModule } from 'primeng/checkbox';
import { SliderModule } from 'primeng/slider';
import { ButtonModule } from 'primeng/button';
import { CardModule } from 'primeng/card';
import { JobService, Job, JobFilters } from '../../core/services/job.service';
import { LocationService, Country, City } from '../../core/services/location.service';
import { CategoryService, Category } from '../../core/services/category.service';
import { JobFilterService, JobType, ExperienceLevel } from '../../core/services/job-filter.service';

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
  selectedCountry: number | null = null;
  selectedLocation: number | null = null;
  selectedCategory: number | null = null;
  selectedLevel = '';
  salaryRange: number[] = [0, 100000];
  salaryRangeInput = { min: 0, max: 100000 };
  salaryMin = 0;
  salaryMax = 100000;

  // 📍 القوائم - من الـ API
  countries: Country[] = [];
  cities: City[] = [];
  jobCategories: Category[] = [];
  employmentTypes: JobType[] = [];
  workPlaces: JobType[] = [];
  jobLevels: ExperienceLevel[] = [];
  
  loading = true;
  totalJobs = 0;

  // 🧭 Pagination
  currentPage = 1;
  itemsPerPage = 6;
  totalPages = 1;

  isFilterOpen = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private jobService: JobService,
    private locationService: LocationService,
    private categoryService: CategoryService,
    private filterService: JobFilterService
  ) {
    this.employmentTypes = this.withFallbackTypes();
    this.workPlaces = this.withFallbackWorkPlaces();
    this.jobLevels = this.withFallbackExperience();
    this.syncSalaryInputsFromRange();
  }

  ngOnInit() {
    this.loadAllData();
    
    this.route.queryParams.subscribe((params) => {
      if (params['search']) {
        this.searchTerm = params['search'];
        this.applyFilters();
      }
    });
  }

  loadAllData() {
    this.loading = true;
    
    // Load filter options
    this.filterService.getFilterOptions().subscribe({
      next: (options) => {
        this.employmentTypes = this.withFallbackTypes(options.types);
        this.workPlaces = this.withFallbackWorkPlaces(options.work_places);
        this.jobLevels = this.withFallbackExperience(options.experience_levels);
        this.salaryMin = options.salary_range.min || 0;
        this.salaryMax = options.salary_range.max || 100000;
        this.salaryRange = [this.salaryMin, this.salaryMax];
        this.syncSalaryInputsFromRange();
        console.log('[Filters] Loaded types:', this.employmentTypes);
        console.log('[Filters] Loaded work places:', this.workPlaces);
        console.log('[Filters] Loaded levels:', this.jobLevels);
      },
      error: (err) => {
        console.error('Error loading filter options:', err);
        this.employmentTypes = this.withFallbackTypes();
        this.workPlaces = this.withFallbackWorkPlaces();
        this.jobLevels = this.withFallbackExperience();
        this.salaryRange = [this.salaryMin, this.salaryMax];
        this.syncSalaryInputsFromRange();
      },
    });

    // Load categories
    this.categoryService.getCategories().subscribe({
      next: (categories) => {
        this.jobCategories = categories;
      },
      error: (err) => {
        console.error('Error loading categories:', err);
        this.jobCategories = [];
      },
    });

    // Load countries
    this.locationService.getCountries().subscribe({
      next: (countries) => {
        this.countries = countries;
      },
      error: (err) => {
        console.error('Error loading countries:', err);
        this.countries = [];
      },
    });

    // Load jobs
    this.loadJobs();
  }

  loadJobs() {
    this.loading = true;
    // Get selected experience levels (from checkboxes)
    const selectedLevelValues = this.jobLevels
      .filter(l => l.selected)
      .map(l => l.value);

    // Build filters object - only include non-null/non-empty values
    const filters: JobFilters = {
      page: this.currentPage,
      per_page: this.itemsPerPage
    };

    // Add optional filters only if they have values
    if (this.searchTerm?.trim()) {
      filters.search = this.searchTerm.trim();
    }
    if (this.selectedCountry) {
      filters.country_id = this.selectedCountry;
    }
    if (this.selectedLocation) {
      filters.city_id = this.selectedLocation;
    }
    if (this.selectedCategory) {
      filters.category_id = this.selectedCategory;
    }
    const selectedWorkTypes = this.employmentTypes
      .filter(t => t.selected)
      .map(t => t.value);
    if (selectedWorkTypes.length === 1) {
      filters.work_type = selectedWorkTypes[0];
    } else if (selectedWorkTypes.length > 1) {
      filters.work_type = selectedWorkTypes.join(',');
    }

    const selectedWorkPlaces = this.workPlaces
      .filter(p => p.selected)
      .map(p => p.value);
    if (selectedWorkPlaces.length === 1) {
      filters.work_place = selectedWorkPlaces[0];
    } else if (selectedWorkPlaces.length > 1) {
      filters.work_place = selectedWorkPlaces.join(',');
    }

    if (selectedLevelValues.length === 1) {
      filters.experience = selectedLevelValues[0];
    } else if (selectedLevelValues.length > 1) {
      filters.experience = selectedLevelValues.join(',');
    } else if (this.selectedLevel) {
      filters.experience = this.selectedLevel;
    }
    if (this.salaryRange[0] > this.salaryMin) {
      filters.min_salary = this.salaryRange[0];
    }
    if (this.salaryRange[1] < this.salaryMax) {
      filters.max_salary = this.salaryRange[1];
    }

    this.jobService.getJobs(filters).subscribe({
      next: (response) => {
        this.jobs = response.data.data;
        this.filteredJobs = [...this.jobs];
        this.paginatedJobs = [...this.jobs];
        this.totalJobs = response.data.total ?? this.jobs.length;
        this.totalPages = response.data.last_page ?? 1;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading jobs:', err);
        this.loading = false;
      },
    });
  }

  toggleFilters() {
    this.isFilterOpen = !this.isFilterOpen;
  }

  onSearchInput() {
    this.applyFilters();
  }

  selectQuickCategory(categoryId: number) {
    this.selectedCategory = categoryId;
    this.currentPage = 1;
    this.applyFilters();
  }

  onCountryChange(countryId: number | null) {
    // Reset city selection when country changes
    this.selectedLocation = null;

    if (countryId) {
      this.locationService.getCities(countryId).subscribe({
        next: (cities) => {
          this.cities = cities;
          this.applyFilters();
        },
        error: (err) => {
          console.error('Error loading cities:', err);
          this.cities = [];
          this.applyFilters();
        },
      });
    } else {
      this.cities = [];
      this.applyFilters();
    }
  }

  applyFiltersAndClose() {
    this.applyFilters();
    this.isFilterOpen = false;
  }

  applyFilters() {
    this.currentPage = 1;
    this.loadJobs();
  }

  resetFilters() {
    this.searchTerm = '';
    this.selectedCategory = null;
    this.selectedCountry = null;
    this.selectedLocation = null;
    this.selectedLevel = '';
    this.employmentTypes.forEach((t) => (t.selected = false));
    this.workPlaces.forEach((p) => (p.selected = false));
    this.jobLevels.forEach((l) => (l.selected = false));
    this.salaryRange = [this.salaryMin, this.salaryMax];
    this.syncSalaryInputsFromRange();
    this.filterService.getFilterOptions().subscribe({
      next: (options) => {
        this.employmentTypes = this.withFallbackTypes(options.types);
        this.workPlaces = this.withFallbackWorkPlaces(options.work_places);
        this.jobLevels = this.withFallbackExperience(options.experience_levels);
        this.salaryMin = options.salary_range.min || 0;
        this.salaryMax = options.salary_range.max || 100000;
        this.salaryRange = [this.salaryMin, this.salaryMax];
        this.syncSalaryInputsFromRange();
        this.cities = [];
        this.currentPage = 1;
        this.applyFilters();
      },
      error: (err) => {
        console.error('Error loading filter options:', err);
        this.employmentTypes = this.withFallbackTypes();
        this.workPlaces = this.withFallbackWorkPlaces();
        this.jobLevels = this.withFallbackExperience();
        this.salaryRange = [this.salaryMin, this.salaryMax];
        this.syncSalaryInputsFromRange();
        this.cities = [];
        this.currentPage = 1;
        this.applyFilters();
      },
    });
  }

  updatePagination() {
    this.paginatedJobs = this.filteredJobs;
  }

  previousPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
      this.loadJobs();
    }
  }

  nextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
      this.loadJobs();
    }
  }

  viewJob(id: number) {
    this.router.navigate(['/job', id]);
  }
  applyForJob(jobId: number) {
    this.router.navigate(['/apply', jobId]);
  }

  formatSalary(job: Job): string {
    return this.jobService.formatSalary(job);
  }

  formatWorkType(job: Job): string {
    return this.jobService.getWorkType(job);
  }

  formatWorkPlace(job: Job): string {
    return this.jobService.getWorkPlace(job);
  }

  trackByValue(_index: number, item: { value: string }): string {
    return item.value;
  }

  onSalarySliderChange() {
    this.syncSalaryInputsFromRange();
    this.applyFilters();
  }

  onSalaryInputChange() {
    let min = this.normalizeSalaryInput(this.salaryRangeInput.min, this.salaryMin);
    let max = this.normalizeSalaryInput(this.salaryRangeInput.max, this.salaryMax);

    if (min > max) {
      if (min - this.salaryMin <= this.salaryMax - max) {
        max = min;
      } else {
        min = max;
      }
    }

    min = Math.max(this.salaryMin, min);
    max = Math.min(this.salaryMax, max);

    this.salaryRange = [min, max];
    this.syncSalaryInputsFromRange();
    this.applyFilters();
  }

  private syncSalaryInputsFromRange(): void {
    this.salaryRangeInput = {
      min: this.salaryRange[0],
      max: this.salaryRange[1],
    };
  }

  private normalizeSalaryInput(value: number | null | undefined, fallback: number): number {
    const parsed = Number(value);
    if (!Number.isFinite(parsed)) {
      return fallback;
    }
    return Math.max(0, parsed);
  }

  private withFallbackTypes(types: JobType[] = []): JobType[] {
    const fallback: JobType[] = [
      { value: 'full-time', name: 'Full Time', count: 0 },
      { value: 'part-time', name: 'Part Time', count: 0 },
      { value: 'freelance', name: 'Freelance', count: 0 },
      { value: 'remote', name: 'Remote', count: 0 },
      { value: 'contract', name: 'Contract', count: 0 },
      { value: 'internship', name: 'Internship', count: 0 }
    ];

    const merged = (types?.length ? types : fallback).map((type) => ({
      ...type,
      name: type.name || this.jobService.getWorkType({ work_type: type.value } as Job),
      selected: type.selected ?? false,
      count: type.count ?? 0,
    }));

    return merged;
  }

  private withFallbackWorkPlaces(workPlaces: JobType[] = []): JobType[] {
    const fallback: JobType[] = [
      { value: 'on-site', name: 'On-site', count: 0 },
      { value: 'remote', name: 'Remote', count: 0 },
      { value: 'hybrid', name: 'Hybrid', count: 0 },
    ];

    const merged = (workPlaces?.length ? workPlaces : fallback).map((place) => ({
      ...place,
      name: place.name || this.jobService.getWorkPlace({ work_place: place.value } as Job),
      selected: place.selected ?? false,
      count: place.count ?? 0,
    }));

    return merged;
  }

  private withFallbackExperience(levels: ExperienceLevel[] = []): ExperienceLevel[] {
    const fallback: ExperienceLevel[] = [
      { value: 'Entry Level', name: 'Entry Level', count: 0 },
      { value: '1-3 years', name: '1-3 years', count: 0 },
      { value: '3-5 years', name: '3-5 years', count: 0 },
      { value: '5+ years', name: '5+ years', count: 0 },
    ];

    const merged = (levels?.length ? levels : fallback).map((level) => ({
      ...level,
      name: level.name || level.value,
      selected: level.selected ?? false,
      count: level.count ?? 0,
    }));

    return merged;
  }
}
