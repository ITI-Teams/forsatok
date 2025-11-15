// candidates-search.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CheckboxModule } from 'primeng/checkbox';
import { ButtonModule } from 'primeng/button';
import { CardModule } from 'primeng/card';
import { CandidateSearchService, CandidateSearchResult, CandidateSearchFilters, Skill, EducationLevel, ExperienceLevel, Country, City } from '../../core/services/candidate-search.service';

@Component({
  selector: 'app-candidates',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    CheckboxModule,
    ButtonModule,
    CardModule
  ],
  templateUrl: './candidates.html',
  styleUrls: ['./candidates.css']
})
export class Candidates implements OnInit {
  candidates: CandidateSearchResult[] = [];
  filteredCandidates: CandidateSearchResult[] = [];

  // Filters
  searchTerm = '';
  selectedCountry: number | null = null;
  selectedCity: number | null = null;
  selectedEducation: string = '';
  selectedSkills: number[] = [];
  selectedExperience: string[] = [];

  // Filter options
  skills: Skill[] = [];
  countries: Country[] = [];
  allCities: City[] = []; // Store all cities from filter options
  cities: City[] = []; // Filtered cities by country
  educationLevels: EducationLevel[] = [];
  experienceLevels: ExperienceLevel[] = [];

  popularSkills: Skill[] = [];

  // Pagination
  currentPage: number = 1;
  itemsPerPage: number = 6;
  totalPages: number = 1;
  totalCandidates: number = 0;

  isFilterOpen: boolean = false;
  loading: boolean = true;
  private isInitialLoad: boolean = true;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private searchService: CandidateSearchService
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
        if (params['education']) {
          this.selectedEducation = params['education'];
        }
        if (params['skill_ids']) {
          this.selectedSkills = params['skill_ids'].split(',').map((id: string) => parseInt(id));
        }
        if (params['experience']) {
          this.selectedExperience = Array.isArray(params['experience']) 
            ? params['experience'] 
            : params['experience'].split(',').filter((exp: string) => exp.trim() !== '');
        }
      }
    });

    // Load all data (filters and candidates)
    this.loadAllData();
  }

  loadAllData() {
    this.loading = true;
    this.loadFilterOptions();
  }

  loadFilterOptions() {
    // Load filter options with all selected filters to update counts dynamically
    const filters: {
      skill_ids?: number[];
      country_id?: number;
      city_id?: number;
      education?: string;
      experience?: string;
    } = {};
    
    if (this.selectedSkills.length > 0) {
      filters.skill_ids = this.selectedSkills;
    }
    if (this.selectedCountry) {
      filters.country_id = this.selectedCountry;
    }
    if (this.selectedCity) {
      filters.city_id = this.selectedCity;
    }
    if (this.selectedEducation) {
      filters.education = this.selectedEducation;
    }
    if (this.selectedExperience.length > 0) {
      filters.experience = this.selectedExperience.join(',');
    }
    
    this.searchService.getFilterOptions(Object.keys(filters).length > 0 ? filters : undefined).subscribe({
      next: (options) => {
        // Load skills with counts
        this.skills = options.skills.map(skill => ({
          ...skill,
          selected: this.selectedSkills.includes(skill.id)
        }));
        // Set popular skills (top 5 by count)
        this.popularSkills = [...this.skills]
          .sort((a, b) => b.count - a.count)
          .slice(0, 5);

        // Load education levels with counts
        this.educationLevels = options.education_levels;

        // Load experience levels with counts
        this.experienceLevels = options.experience_levels;

        // Load countries with counts
        this.countries = options.countries || [];

        // Load all cities with counts
        this.allCities = options.cities || [];

        // Load candidates after filter options are loaded (only on initial load)
        if (this.isInitialLoad) {
          this.loadCandidates();
          this.isInitialLoad = false;
        }
      },
      error: (err) => {
        console.error('Error loading filter options:', err);
        this.skills = [];
        this.educationLevels = [];
        this.experienceLevels = [];
        this.countries = [];
        // Still try to load candidates even if filter options fail
        if (this.isInitialLoad) {
          this.loadCandidates();
          this.isInitialLoad = false;
        }
      }
    });
  }

  loadCandidates() {
    this.loading = true;

    const filters: CandidateSearchFilters = {
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
    if (this.selectedEducation) {
      filters.education = this.selectedEducation;
    }
    if (this.selectedSkills.length > 0) {
      filters.skill_ids = this.selectedSkills;
    }
    if (this.selectedExperience.length > 0) {
      filters.experience = this.selectedExperience;
    }

    this.searchService.searchCandidates(filters).subscribe({
      next: (response) => {
        console.log('Candidates API Response:', response);
        this.candidates = response.data || [];
        this.filteredCandidates = [...this.candidates];
        this.totalCandidates = response.meta?.total ?? this.candidates.length;
        this.totalPages = response.meta?.last_page ?? 1;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading candidates:', err);
        this.candidates = [];
        this.filteredCandidates = [];
        this.totalCandidates = 0;
        this.totalPages = 1;
        this.loading = false;
        // Show user-friendly error message
        if (err.status === 0) {
          console.error('Network error: Please check your internet connection');
        } else if (err.status >= 500) {
          console.error('Server error: Please try again later');
        } else {
          console.error('Error loading candidates: Please try again');
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

  onSkillToggle(skillId: number) {
    const index = this.selectedSkills.indexOf(skillId);
    if (index > -1) {
      this.selectedSkills.splice(index, 1);
    } else {
      this.selectedSkills.push(skillId);
    }
    // Reload filter options to update counts based on selected skills
    this.loadFilterOptions();
    this.applyFilters();
  }

  onExperienceToggle(experienceValue: string) {
    const index = this.selectedExperience.indexOf(experienceValue);
    if (index > -1) {
      this.selectedExperience.splice(index, 1);
    } else {
      this.selectedExperience.push(experienceValue);
    }
    // Reload filter options to update counts
    this.loadFilterOptions();
    this.applyFilters();
  }

  onCityChange() {
    // Reload filter options to update counts
    this.loadFilterOptions();
    this.applyFilters();
  }

  onEducationChange() {
    // Reload filter options to update counts
    this.loadFilterOptions();
    this.applyFilters();
  }

  applyFilters() {
    this.currentPage = 1;
    this.updateURL();
    this.loadCandidates();
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

    if (this.selectedEducation) {
      queryParams.education = this.selectedEducation;
    } else {
      queryParams.education = null;
    }

    if (this.selectedSkills.length > 0) {
      queryParams.skill_ids = this.selectedSkills.join(',');
    } else {
      queryParams.skill_ids = null;
    }

    if (this.selectedExperience.length > 0) {
      queryParams.experience = this.selectedExperience.join(',');
    } else {
      queryParams.experience = null;
    }

    // Remove null values
    Object.keys(queryParams).forEach(key => {
      if (queryParams[key] === null) {
        delete queryParams[key];
      }
    });

    // Update URL without reloading
    this.router.navigate([], {
      relativeTo: this.route,
      queryParams: queryParams,
      replaceUrl: true
    });
  }

  get paginatedCandidates() {
    return this.filteredCandidates;
  }

  nextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
      this.loadCandidates();
    }
  }

  previousPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
      this.loadCandidates();
    }
  }

  resetFilters() {
    this.searchTerm = '';
    this.selectedCountry = null;
    this.selectedCity = null;
    this.selectedEducation = '';
    this.selectedSkills = [];
    this.selectedExperience = [];
    this.cities = [];
    this.skills.forEach(skill => {
      if (skill.selected !== undefined) {
        skill.selected = false;
      }
    });
    this.currentPage = 1;
    this.router.navigate([], {
      relativeTo: this.route,
      queryParams: {},
      replaceUrl: true
    });
    this.applyFilters();
  }

  selectQuickSkill(skillId: number) {
    if (!this.selectedSkills.includes(skillId)) {
      this.selectedSkills.push(skillId);
    }
    this.applyFilters();

    setTimeout(() => {
      document.querySelector('.candidates-listings')?.scrollIntoView({
        behavior: 'smooth'
      });
    }, 100);
  }

  contactCandidate(email: string) {
    if (email) {
      window.location.href = `mailto:${email}`;
    }
  }

  viewProfile(candidateId: number) {
    this.router.navigate(['/candidate', candidateId]);
  }

  getCandidateLocation(candidate: CandidateSearchResult): string {
    if (candidate.location?.city) {
      return candidate.location.city.name;
    }
    if (candidate.location?.country) {
      return candidate.location.country.name;
    }
    return 'Not specified';
  }

  getCandidateSkills(candidate: CandidateSearchResult): string[] {
    if (candidate.skills && candidate.skills.length > 0) {
      return candidate.skills.map(skill => skill.name);
    }
    return [];
  }
}
