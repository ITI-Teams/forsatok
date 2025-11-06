// job-search.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CheckboxModule } from 'primeng/checkbox';
import { SliderModule } from 'primeng/slider';
import { ButtonModule } from 'primeng/button';
import { CardModule } from 'primeng/card';

interface Job {
  id: number;
  title: string;
  company: string;
  type: string;
  category: string;
  level: string;
  salary: number;
  location: string;
  description: string;
}

@Component({
  selector: 'app-jobs',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    CheckboxModule,
    SliderModule,
    ButtonModule,
    CardModule
  ],
  templateUrl: './jobs.html',
  styleUrls: ['./jobs.css']
})
export class Jobs implements OnInit {
  jobs: Job[] = [];
  filteredJobs: Job[] = [];
  selectedLocation: string = '';
  selectedCategory: string = '';
  currentPage: number = 1;
  itemsPerPage: number = 10;

  // Filter options
  employmentTypes = [
    { name: 'Full Time', count: 200, selected: false },
    { name: 'Part Time', count: 120, selected: false },
    { name: 'Remote', count: 60, selected: false },
    { name: 'Internalize', count: 98, selected: false },
    { name: 'Freebase', count: 150, selected: false }
  ];

  jobCategories = [
    { name: 'Creative Design', value: 'creative-design' , count: 200, selected: false },
    { name: 'Development', value: 'development', count: 120, selected: false },
    { name: 'Marketing', value: 'marketing', count: 90, selected: false },
    { name: 'Customer Care', value: 'customer-Care' , count: 98, selected: false },
    { name: 'Customer Service', value: 'finance', count: 150, selected: false },
    { name: 'Finance', value: 'finance', count: 150, selected: false }
  ];

  jobLevels = [
    { name: 'Entry Level', count: 1000, selected: false },
    { name: 'Mid Level', count: 120, selected: false },
    { name: 'Senior Level', count: 90, selected: false },
    { name: 'Director', count: 98, selected: false },
    { name: 'VP', count: 150, selected: false }
  ];

  locations = [
    { name: 'New York', value: 'new-york' },
    { name: 'San Francisco', value: 'san-francisco' },
    { name: 'London', value: 'london' },
    { name: 'Berlin', value: 'berlin' },
    { name: 'Tokyo', value: 'tokyo' },
    { name: 'Remote', value: 'remote' }
  ];

  salaryRange: number[] = [1000, 8000];
  searchTerm: string = '';

  ngOnInit() {
    // Mock data - replace with actual API call
    this.jobs = [
      { id: 1, title: 'Product Designer', company: "User's Agency + Mobile Editor", type: 'Full Time', category: 'Creative Design', level: 'Mid Level', salary: 6000, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 2, title: 'Full Stack Engineer', company: 'Audio Editor + Web Services', type: 'Full Time', category: 'Development', level: 'Senior Level', salary: 8000, location: 'london', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 3, title: 'Ads Management', company: 'Google + Customs AG', type: 'Part Time', category: 'Marketing', level: 'Entry Level', salary: 3000, location: 'san-francisco', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 4, title: 'UI/UX Designer', company: 'Business + YouTube, iOS', type: 'Remote', category: 'Creative Design', level: 'Mid Level', salary: 5500, location: 'new-york', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 5, title: 'Brand Designer', company: 'Alista Chrome + Mobile Editor', type: 'Full Time', category: 'Creative Design', level: 'Senior Level', salary: 7000, location: 'san-francisco', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' },
      { id: 6, title: 'Social Media Officer', company: 'Android UI + Zigzag, Canada', type: 'Part Time', category: 'Marketing', level: 'Entry Level', salary: 2500, location: 'london', description: 'Full Name $9697-Homey $4,9999 $2,000 $6,000 $5,000' }
    ];

    this.filteredJobs = [...this.jobs];
  }

  applyFilters() {
    this.filteredJobs = this.jobs.filter(job => {
      // Filter by employment type
      const typeSelected = this.employmentTypes.some(type => type.selected);
      if (typeSelected && !this.employmentTypes.find(type => type.name === job.type && type.selected)) {
        return false;
      }

      // Filter by job category
      if (this.selectedCategory && job.category.toLowerCase().replace(/\s+/g, '-') !== this.selectedCategory.toLowerCase()) {
        return false;
      }

      // Filter by job level
      const levelSelected = this.jobLevels.some(level => level.selected);
      if (levelSelected && !this.jobLevels.find(level => level.name === job.level && level.selected)) {
        return false;
      }

      // Filter by salary range
      if (job.salary < this.salaryRange[0] || job.salary > this.salaryRange[1]) {
        return false;
      }

      // Filter by search term
      if (this.searchTerm && !job.title.toLowerCase().includes(this.searchTerm.toLowerCase()) &&
        !job.company.toLowerCase().includes(this.searchTerm.toLowerCase())) {
        return false;
      }

      // Filter by location (if selected)
      if (this.selectedLocation && job.location !== this.selectedLocation) {
        return false;
      }


      return true;
    });
  }
  get paginatedJobs() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    return this.filteredJobs.slice(startIndex, endIndex);
  }
  get totalPages() {
    return Math.ceil(this.filteredJobs.length / this.itemsPerPage);
  }

  nextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
    }
  }

  previousPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
    }
  }

  resetFilters() {
    this.employmentTypes.forEach(type => type.selected = false);
    this.jobCategories.forEach(category => category.selected = false);
    this.jobLevels.forEach(level => level.selected = false);
    this.salaryRange = [1000, 8000];
    this.searchTerm = '';
    this.selectedLocation = '';
    this.applyFilters();
  }

  selectQuickCategory(categoryValue: string) {
    this.selectedCategory = categoryValue;
    this.applyFilters();

    setTimeout(() => {
      document.querySelector('.job-listings')?.scrollIntoView({
        behavior: 'smooth'
      });
    }, 100);
  }

}
