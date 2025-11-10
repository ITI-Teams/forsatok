import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CheckboxModule } from 'primeng/checkbox';
import { ButtonModule } from 'primeng/button';

interface Company {
  id: number;
  name: string;
  logo: string;
  location: string;
  industry: string;
  jobsCount: number;
  description: string;
}

@Component({
  selector: 'app-companies',
  standalone: true,
  imports: [CommonModule, FormsModule, CheckboxModule, ButtonModule],
  templateUrl: './companies-list.html',
  styleUrls: ['./companies-list.css']
})
export class CompaniesList implements OnInit {
  companies: Company[] = [];
  filteredCompanies: Company[] = [];
  searchTerm = '';
  selectedLocation = '';
  selectedIndustry = '';
  isFilterOpen = false;
  currentPage = 1;
  itemsPerPage = 8;

  locations = [
    { name: 'New York', value: 'new-york' },
    { name: 'London', value: 'london' },
    { name: 'Tokyo', value: 'tokyo' },
    { name: 'Berlin', value: 'berlin' },
    { name: 'San Francisco', value: 'san-francisco' },
    { name: 'Remote', value: 'remote' }
  ];

  industries = [
    { name: 'Technology', value: 'technology' },
    { name: 'Finance', value: 'finance' },
    { name: 'Healthcare', value: 'healthcare' },
    { name: 'Retail', value: 'retail' },
    { name: 'Marketing', value: 'marketing' },
    { name: 'Design', value: 'design' }
  ];

  constructor(private route: ActivatedRoute, private router: Router) {}

  ngOnInit() {
    this.companies = [
      {
        id: 1,
        name: 'Shiseido Co',
        logo: 'https://logo.clearbit.com/shiseido.com',
        location: 'tokyo',
        industry: 'retail',
        jobsCount: 8,
        description: 'Global beauty & cosmetics company.'
      },
      {
        id: 2,
        name: 'OpenAI',
        logo: 'https://logo.clearbit.com/openai.com',
        location: 'san-francisco',
        industry: 'technology',
        jobsCount: 25,
        description: 'Artificial intelligence research and deployment company.'
      },
      {
        id: 3,
        name: 'HSBC Bank',
        logo: 'https://logo.clearbit.com/hsbc.com',
        location: 'london',
        industry: 'finance',
        jobsCount: 10,
        description: 'Global financial services organization.'
      },
      {
        id: 4,
        name: 'Figma Inc.',
        logo: 'https://logo.clearbit.com/figma.com',
        location: 'new-york',
        industry: 'design',
        jobsCount: 6,
        description: 'Collaborative design platform for teams.'
      }
    ];

    this.filteredCompanies = [...this.companies];

    this.route.queryParams.subscribe(params => {
      if (params['search']) this.searchTerm = params['search'];
      if (params['location']) this.selectedLocation = params['location'];
      if (params['industry']) this.selectedIndustry = params['industry'];
      this.applyFilters(false);
    });
  }

  toggleFilters() {
    this.isFilterOpen = !this.isFilterOpen;
  }

  applyFilters(updateUrl: boolean = true) {
    this.filteredCompanies = this.companies.filter(c => {
      if (this.searchTerm && !c.name.toLowerCase().includes(this.searchTerm.toLowerCase())) return false;
      if (this.selectedLocation && c.location !== this.selectedLocation) return false;
      if (this.selectedIndustry && c.industry !== this.selectedIndustry) return false;
      return true;
    });

    this.currentPage = 1;
    if (updateUrl) this.updateURL();
  }

  updateURL() {
    const queryParams: any = {};
    if (this.searchTerm) queryParams.search = this.searchTerm;
    if (this.selectedLocation) queryParams.location = this.selectedLocation;
    if (this.selectedIndustry) queryParams.industry = this.selectedIndustry;

    this.router.navigate([], {
      relativeTo: this.route,
      queryParams,
      replaceUrl: true
    });
  }

  resetFilters() {
    this.searchTerm = '';
    this.selectedLocation = '';
    this.selectedIndustry = '';
    this.applyFilters();
  }

  get paginatedCompanies() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.filteredCompanies.slice(start, start + this.itemsPerPage);
  }

  get totalPages() {
    return Math.ceil(this.filteredCompanies.length / this.itemsPerPage);
  }

  nextPage() {
    if (this.currentPage < this.totalPages) this.currentPage++;
  }

  previousPage() {
    if (this.currentPage > 1) this.currentPage--;
  }

  viewCompany(id: number) {
    this.router.navigate(['/company', id]);
  }

  applyFiltersAndClose() {
    this.applyFilters();
    this.toggleFilters();
  }
}
