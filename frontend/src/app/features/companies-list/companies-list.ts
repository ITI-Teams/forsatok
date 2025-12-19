import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CheckboxModule } from 'primeng/checkbox';
import { ButtonModule } from 'primeng/button';
import { CompanySearchService, CompanySearchResult, CompanySearchFilters, Industry, Country, City } from '../../core/services/company-search.service';
import {environment} from '../../environments/environment';

@Component({
  selector: 'app-companies',
  standalone: true,
  imports: [CommonModule, FormsModule, CheckboxModule, ButtonModule],
  templateUrl: './companies-list.html',
  styleUrls: ['./companies-list.css']
})
export class CompaniesList implements OnInit {
  companies: CompanySearchResult[] = [];
  filteredCompanies: CompanySearchResult[] = [];

  // Filters
  searchTerm = '';
  selectedCountry: number | null = null;
  selectedCity: number | null = null;
  selectedIndustry: string = '';

  // Filter options
  industries: Industry[] = [];
  countries: Country[] = [];
  allCities: City[] = []; // Store all cities from filter options
  cities: City[] = []; // Filtered cities by country

  // Pagination
  currentPage: number = 1;
  itemsPerPage: number = 8;
  totalPages: number = 1;
  totalCompanies: number = 0;

  isFilterOpen: boolean = false;
  loading: boolean = true;
  private isInitialLoad: boolean = true;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private searchService: CompanySearchService
  ) {}

  ngOnInit() {
    // Read filters from URL query params first
    this.route.queryParams.subscribe(params => {
      if (this.isInitialLoad) {
        if (params['search']) {
          this.searchTerm = params['search'];
        }
        if (params['country_id']) {
          this.selectedCountry = parseInt(params['country_id']);
          this.loadCities(this.selectedCountry);
        }
        if (params['city_id']) {
          this.selectedCity = parseInt(params['city_id']);
        }
        if (params['industry']) {
          this.selectedIndustry = params['industry'];
        }
        if (params['page']) {
          this.currentPage = parseInt(params['page']);
        }
      }
    });

    // Load all data (filters and companies)
    this.loadAllData();
  }

  loadAllData() {
    this.loading = true;
    this.loadFilterOptions();
  }

  loadFilterOptions() {
    // Load filter options with all selected filters to update counts dynamically
    const filters: {
      country_id?: number;
      city_id?: number;
      industry?: string;
    } = {};

    if (this.selectedCountry) {
      filters.country_id = this.selectedCountry;
    }
    if (this.selectedCity) {
      filters.city_id = this.selectedCity;
    }
    if (this.selectedIndustry) {
      filters.industry = this.selectedIndustry;
    }

    this.searchService.getFilterOptions(Object.keys(filters).length > 0 ? filters : undefined).subscribe({
      next: (options) => {
        // Load industries with counts
        this.industries = options.industries;

        // Load countries with counts
        this.countries = options.countries || [];

        // Load all cities with counts
        this.allCities = options.cities || [];

        // Load companies after filter options are loaded (only on initial load)
        if (this.isInitialLoad) {
          this.loadCompanies();
          this.isInitialLoad = false;
        }
      },
      error: (err) => {
        console.error('Error loading filter options:', err);
        this.industries = [];
        this.countries = [];
        // Still try to load companies even if filter options fail
        if (this.isInitialLoad) {
          this.loadCompanies();
          this.isInitialLoad = false;
        }
      }
    });
  }

  loadCompanies() {
    this.loading = true;

    const filters: CompanySearchFilters = {
      page: this.currentPage,
      per_page: this.itemsPerPage
    };

    if (this.searchTerm?.trim()) {
      filters.search = this.searchTerm.trim();
    }
    if (this.selectedCountry) {
      filters.country_id = this.selectedCountry;
    }
    if (this.selectedCity) {
      filters.city_id = this.selectedCity;
    }
    if (this.selectedIndustry) {
      filters.industry = this.selectedIndustry;
    }

    this.searchService.searchCompanies(filters).subscribe({
      next: (response) => {
        this.companies = response.data || [];
        this.filteredCompanies = [...this.companies];
        this.totalCompanies = response.meta?.total ?? this.companies.length;
        this.totalPages = response.meta?.last_page ?? 1;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading companies:', err);
        this.companies = [];
        this.filteredCompanies = [];
        this.totalCompanies = 0;
        this.totalPages = 1;
        this.loading = false;
        // Show user-friendly error message
        if (err.status === 0) {
          console.error('Network error: Please check your internet connection');
        } else if (err.status >= 500) {
          console.error('Server error: Please try again later');
        } else {
          console.error('Error loading companies: Please try again');
        }
      }
    });
  }

  toggleFilters() {
    this.isFilterOpen = !this.isFilterOpen;
  }

  applyFiltersAndClose() {
    this.applyFilters();
    this.isFilterOpen = false;
  }

  onSearchInput() {
    this.applyFilters();
  }

  onCountryChange(countryId: number | null) {
    this.selectedCity = null;
    this.selectedCountry = countryId;
    if (countryId) {
      this.loadCities(countryId);
    } else {
      this.cities = [];
    }
    // Reload filter options to update counts
    this.loadFilterOptions();
    this.applyFilters();
  }

  loadCities(countryId: number) {
    // Filter cities from already loaded filter options by country_id
    this.cities = this.allCities.filter(city => city.country_id === countryId);
  }

  onIndustryChange() {
    // Reload filter options to update counts
    this.loadFilterOptions();
    this.applyFilters();
  }

  onCityChange() {
    // Reload filter options to update counts
    this.loadFilterOptions();
    this.applyFilters();
  }

  applyFilters() {
    this.currentPage = 1;
    this.updateURL();
    this.loadCompanies();
  }

  updateURL() {
    const queryParams: any = {};

    if (this.searchTerm?.trim()) {
      queryParams.search = this.searchTerm.trim();
    } else {
      queryParams.search = null;
    }

    if (this.selectedCountry) {
      queryParams.country_id = this.selectedCountry.toString();
    } else {
      queryParams.country_id = null;
    }

    if (this.selectedCity) {
      queryParams.city_id = this.selectedCity.toString();
    } else {
      queryParams.city_id = null;
    }

    if (this.selectedIndustry) {
      queryParams.industry = this.selectedIndustry;
    } else {
      queryParams.industry = null;
    }

    // Remove null values
    Object.keys(queryParams).forEach(key => {
      if (queryParams[key] === null) {
        delete queryParams[key];
      }
    });

    this.router.navigate([], {
      relativeTo: this.route,
      queryParams,
      replaceUrl: true
    });
  }

  resetFilters() {
    this.searchTerm = '';
    this.selectedCountry = null;
    this.selectedCity = null;
    this.selectedIndustry = '';
    this.cities = [];
    this.applyFilters();
  }

  get paginatedCompanies() {
    return this.filteredCompanies;
  }

  nextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
      this.updateURL();
      this.loadCompanies();
    }
  }

  previousPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
      this.updateURL();
      this.loadCompanies();
    }
  }

  viewCompany(id: number) {
    this.router.navigate(['/company', id]);
  }
}
