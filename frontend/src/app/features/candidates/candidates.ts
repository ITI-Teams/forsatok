// candidates-search.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CheckboxModule } from 'primeng/checkbox';
import { SliderModule } from 'primeng/slider';
import { ButtonModule } from 'primeng/button';
import { CardModule } from 'primeng/card';

interface Candidate {
  id: number;
  name: string;
  email: string;
  phone?: string;
  location?: string;
  education?: string;
  experience?: string;
  bio?: string;
  skills: string[];
}

@Component({
  selector: 'app-candidates',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    CheckboxModule,
    SliderModule,
    ButtonModule,
    CardModule
  ],
  templateUrl: './candidates.html',
  styleUrls: ['./candidates.css']
})
export class Candidates implements OnInit {
  candidates: Candidate[] = [];
  filteredCandidates: Candidate[] = [];
  selectedLocation: string = '';
  selectedEducation: string = '';
  currentPage: number = 1;
  itemsPerPage: number = 10;
  isFilterOpen: boolean = false;
  private isInitialLoad: boolean = true;

  constructor(
    private route: ActivatedRoute,
    private router: Router
  ) {}

  toggleFilters() {
    this.isFilterOpen = !this.isFilterOpen;
  }

  applyFiltersAndClose() {
    this.applyFilters();
    this.isFilterOpen = false;
  }

  // Filter options
  skills = [
    { name: 'JavaScript', count: 150, selected: false },
    { name: 'Python', count: 120, selected: false },
    { name: 'React', count: 90, selected: false },
    { name: 'Angular', count: 85, selected: false },
    { name: 'Vue.js', count: 70, selected: false },
    { name: 'Node.js', count: 95, selected: false },
    { name: 'PHP', count: 80, selected: false },
    { name: 'Laravel', count: 75, selected: false },
    { name: 'Java', count: 100, selected: false },
    { name: 'C++', count: 60, selected: false },
    { name: 'UI/UX Design', count: 110, selected: false },
    { name: 'Graphic Design', count: 85, selected: false },
    { name: 'Marketing', count: 95, selected: false },
    { name: 'Sales', count: 80, selected: false },
    { name: 'Project Management', count: 70, selected: false }
  ];

  educationLevels = [
    { name: 'High School', value: 'high-school' },
    { name: 'Bachelor\'s Degree', value: 'bachelor' },
    { name: 'Master\'s Degree', value: 'master' },
    { name: 'PhD', value: 'phd' },
    { name: 'Diploma', value: 'diploma' },
    { name: 'Certificate', value: 'certificate' }
  ];

  locations = [
    { name: 'New York', value: 'new-york' },
    { name: 'San Francisco', value: 'san-francisco' },
    { name: 'London', value: 'london' },
    { name: 'Berlin', value: 'berlin' },
    { name: 'Tokyo', value: 'tokyo' },
    { name: 'Remote', value: 'remote' },
    { name: 'Cairo', value: 'cairo' },
    { name: 'Dubai', value: 'dubai' }
  ];

  popularSkills = [
    { name: 'JavaScript', value: 'JavaScript' },
    { name: 'Python', value: 'Python' },
    { name: 'React', value: 'React' },
    { name: 'UI/UX Design', value: 'UI/UX Design' },
    { name: 'Marketing', value: 'Marketing' }
  ];

  experienceRange: number[] = [0, 20];
  searchTerm: string = '';

  ngOnInit() {
    // Mock data - replace with actual API call
    this.candidates = [
      {
        id: 1,
        name: 'Ahmed Mohamed',
        email: 'ahmed.mohamed@example.com',
        phone: '+20 123 456 7890',
        location: 'Cairo',
        education: 'Bachelor\'s Degree',
        experience: '5',
        bio: 'Experienced full-stack developer with 5+ years in web development. Specialized in JavaScript, React, and Node.js.',
        skills: ['JavaScript', 'React', 'Node.js', 'MongoDB']
      },
      {
        id: 2,
        name: 'Sara Ali',
        email: 'sara.ali@example.com',
        location: 'Dubai',
        education: 'Master\'s Degree',
        experience: '3',
        bio: 'Creative UI/UX designer passionate about creating beautiful and functional user interfaces.',
        skills: ['UI/UX Design', 'Figma', 'Adobe XD', 'Graphic Design']
      },
      {
        id: 3,
        name: 'Mohamed Hassan',
        email: 'mohamed.hassan@example.com',
        phone: '+20 987 654 3210',
        location: 'Cairo',
        education: 'Bachelor\'s Degree',
        experience: '7',
        bio: 'Senior software engineer with expertise in Python, Django, and cloud technologies.',
        skills: ['Python', 'Django', 'AWS', 'Docker']
      },
      {
        id: 4,
        name: 'Fatima Ibrahim',
        email: 'fatima.ibrahim@example.com',
        location: 'Remote',
        education: 'Master\'s Degree',
        experience: '4',
        bio: 'Digital marketing specialist with proven track record in social media and content marketing.',
        skills: ['Marketing', 'Social Media', 'Content Writing', 'SEO']
      },
      {
        id: 5,
        name: 'Omar Khaled',
        email: 'omar.khaled@example.com',
        phone: '+20 555 123 4567',
        location: 'Cairo',
        education: 'Bachelor\'s Degree',
        experience: '2',
        bio: 'Frontend developer specializing in Angular and modern JavaScript frameworks.',
        skills: ['Angular', 'TypeScript', 'JavaScript', 'HTML/CSS']
      },
      {
        id: 6,
        name: 'Layla Mahmoud',
        email: 'layla.mahmoud@example.com',
        location: 'Dubai',
        education: 'PhD',
        experience: '10',
        bio: 'Senior data scientist with expertise in machine learning and big data analytics.',
        skills: ['Python', 'Machine Learning', 'Data Science', 'TensorFlow']
      },
      {
        id: 7,
        name: 'Youssef Nour',
        email: 'youssef.nour@example.com',
        location: 'Cairo',
        education: 'Bachelor\'s Degree',
        experience: '6',
        bio: 'Full-stack developer with strong background in PHP and Laravel framework.',
        skills: ['PHP', 'Laravel', 'MySQL', 'Vue.js']
      },
      {
        id: 8,
        name: 'Nour Ahmed',
        email: 'nour.ahmed@example.com',
        location: 'Remote',
        education: 'Master\'s Degree',
        experience: '4',
        bio: 'Product manager with experience in agile methodologies and product development.',
        skills: ['Product Management', 'Agile', 'Project Management', 'Analytics']
      },
      {
        id: 9,
        name: 'Karim Mostafa',
        email: 'karim.mostafa@example.com',
        phone: '+20 111 222 3333',
        location: 'Cairo',
        education: 'Bachelor\'s Degree',
        experience: '3',
        bio: 'Backend developer specializing in Java and Spring framework with microservices experience.',
        skills: ['Java', 'Spring', 'Microservices', 'PostgreSQL']
      },
      {
        id: 10,
        name: 'Mariam Saleh',
        email: 'mariam.saleh@example.com',
        location: 'Dubai',
        education: 'Bachelor\'s Degree',
        experience: '5',
        bio: 'Sales professional with expertise in B2B sales and customer relationship management.',
        skills: ['Sales', 'CRM', 'Negotiation', 'Communication']
      },
      {
        id: 11,
        name: 'Hassan Tarek',
        email: 'hassan.tarek@example.com',
        location: 'Cairo',
        education: 'Master\'s Degree',
        experience: '8',
        bio: 'DevOps engineer with expertise in CI/CD, Kubernetes, and cloud infrastructure.',
        skills: ['DevOps', 'Kubernetes', 'Docker', 'AWS', 'CI/CD']
      },
      {
        id: 12,
        name: 'Dina Samir',
        email: 'dina.samir@example.com',
        location: 'Remote',
        education: 'Bachelor\'s Degree',
        experience: '2',
        bio: 'Junior frontend developer with passion for creating responsive and accessible web applications.',
        skills: ['React', 'JavaScript', 'CSS', 'HTML']
      }
    ];

    this.filteredCandidates = [...this.candidates];

    // Read filters from URL query params
    this.route.queryParams.subscribe(params => {
      if (this.isInitialLoad) {
        if (params['search']) {
          this.searchTerm = params['search'];
        }
        if (params['location']) {
          this.selectedLocation = params['location'];
        }
        if (params['education']) {
          this.selectedEducation = params['education'];
        }
        if (params['skills']) {
          const skillNames = params['skills'].split(',');
          this.skills.forEach(skill => {
            skill.selected = skillNames.includes(skill.name.toLowerCase().replace(/\s+/g, '-'));
          });
        }
        if (params['experienceMin'] && params['experienceMax']) {
          this.experienceRange = [parseInt(params['experienceMin']), parseInt(params['experienceMax'])];
        }
        // Apply filters after reading from URL (without updating URL)
        this.applyFiltersWithoutURLUpdate();
        this.isInitialLoad = false;
      }
    });
  }

  applyFiltersWithoutURLUpdate() {
    this.filteredCandidates = this.candidates.filter(candidate => {
      // Filter by skills
      const skillsSelected = this.skills.some(skill => skill.selected);
      if (skillsSelected) {
        const candidateHasSelectedSkill = this.skills.some(skill =>
          skill.selected && candidate.skills.some(cs =>
            cs.toLowerCase().includes(skill.name.toLowerCase())
          )
        );
        if (!candidateHasSelectedSkill) {
          return false;
        }
      }

      // Filter by education
      if (this.selectedEducation && candidate.education) {
        const educationValue = candidate.education.toLowerCase().replace(/\s+/g, '-');
        if (!educationValue.includes(this.selectedEducation.toLowerCase())) {
          return false;
        }
      }

      // Filter by experience range
      const candidateExp = parseInt(candidate.experience || '0');
      if (candidateExp < this.experienceRange[0] || candidateExp > this.experienceRange[1]) {
        return false;
      }

      // Filter by search term (name, email, or skills)
      if (this.searchTerm) {
        const searchLower = this.searchTerm.toLowerCase();
        const matchesName = candidate.name.toLowerCase().includes(searchLower);
        const matchesEmail = candidate.email.toLowerCase().includes(searchLower);
        const matchesSkills = candidate.skills.some(skill =>
          skill.toLowerCase().includes(searchLower)
        );
        const matchesBio = candidate.bio?.toLowerCase().includes(searchLower);

        if (!matchesName && !matchesEmail && !matchesSkills && !matchesBio) {
          return false;
        }
      }

      // Filter by location (if selected)
      if (this.selectedLocation && candidate.location) {
        const locationValue = candidate.location.toLowerCase().replace(/\s+/g, '-');
        if (locationValue !== this.selectedLocation.toLowerCase()) {
          return false;
        }
      }

      return true;
    });

    // Reset to first page when filters change
    this.currentPage = 1;
  }

  updateURL() {
    const queryParams: any = {};
    
    if (this.searchTerm?.trim()) {
      queryParams.search = this.searchTerm.trim();
    } else {
      queryParams.search = null;
    }
    
    if (this.selectedLocation?.trim()) {
      queryParams.location = this.selectedLocation.trim();
    } else {
      queryParams.location = null;
    }
    
    if (this.selectedEducation?.trim()) {
      queryParams.education = this.selectedEducation.trim();
    } else {
      queryParams.education = null;
    }
    
    const selectedSkills = this.skills
      .filter(skill => skill.selected)
      .map(skill => skill.name.toLowerCase().replace(/\s+/g, '-'));
    if (selectedSkills.length > 0) {
      queryParams.skills = selectedSkills.join(',');
    } else {
      queryParams.skills = null;
    }
    
    if (this.experienceRange[0] !== 0 || this.experienceRange[1] !== 20) {
      queryParams.experienceMin = this.experienceRange[0].toString();
      queryParams.experienceMax = this.experienceRange[1].toString();
    } else {
      queryParams.experienceMin = null;
      queryParams.experienceMax = null;
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

  applyFilters() {
    this.filteredCandidates = this.candidates.filter(candidate => {
      // Filter by skills
      const skillsSelected = this.skills.some(skill => skill.selected);
      if (skillsSelected) {
        const candidateHasSelectedSkill = this.skills.some(skill =>
          skill.selected && candidate.skills.some(cs =>
            cs.toLowerCase().includes(skill.name.toLowerCase())
          )
        );
        if (!candidateHasSelectedSkill) {
          return false;
        }
      }

      // Filter by education
      if (this.selectedEducation && candidate.education) {
        const educationValue = candidate.education.toLowerCase().replace(/\s+/g, '-');
        if (!educationValue.includes(this.selectedEducation.toLowerCase())) {
          return false;
        }
      }

      // Filter by experience range
      const candidateExp = parseInt(candidate.experience || '0');
      if (candidateExp < this.experienceRange[0] || candidateExp > this.experienceRange[1]) {
        return false;
      }

      // Filter by search term (name, email, or skills)
      if (this.searchTerm) {
        const searchLower = this.searchTerm.toLowerCase();
        const matchesName = candidate.name.toLowerCase().includes(searchLower);
        const matchesEmail = candidate.email.toLowerCase().includes(searchLower);
        const matchesSkills = candidate.skills.some(skill =>
          skill.toLowerCase().includes(searchLower)
        );
        const matchesBio = candidate.bio?.toLowerCase().includes(searchLower);

        if (!matchesName && !matchesEmail && !matchesSkills && !matchesBio) {
          return false;
        }
      }

      // Filter by location (if selected)
      if (this.selectedLocation && candidate.location) {
        const locationValue = candidate.location.toLowerCase().replace(/\s+/g, '-');
        if (locationValue !== this.selectedLocation.toLowerCase()) {
          return false;
        }
      }

      return true;
    });

    // Reset to first page when filters change
    this.currentPage = 1;
    
    // Update URL when filters change (only if not initial load)
    if (!this.isInitialLoad) {
      this.updateURL();
    }
  }

  get paginatedCandidates() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    return this.filteredCandidates.slice(startIndex, endIndex);
  }

  get totalPages() {
    return Math.ceil(this.filteredCandidates.length / this.itemsPerPage);
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
    this.skills.forEach(skill => skill.selected = false);
    this.experienceRange = [0, 20];
    this.searchTerm = '';
    this.selectedLocation = '';
    this.selectedEducation = '';
    // Clear URL params
    this.router.navigate([], {
      relativeTo: this.route,
      queryParams: {},
      replaceUrl: true
    });
    this.applyFilters();
  }

  selectQuickSkill(skillValue: string) {
    const skill = this.skills.find(s =>
      s.name.toLowerCase().includes(skillValue.toLowerCase()) ||
      skillValue.toLowerCase().includes(s.name.toLowerCase())
    );
    if (skill) {
      skill.selected = true;
    }
    this.applyFilters();

    setTimeout(() => {
      document.querySelector('.candidates-listings')?.scrollIntoView({
        behavior: 'smooth'
      });
    }, 100);
  }

  onSearchInput() {
    // Debounce search input to avoid too many URL updates
    clearTimeout((this as any).searchTimeout);
    (this as any).searchTimeout = setTimeout(() => {
      this.applyFilters();
    }, 500);
  }

  contactCandidate(email: string) {
    if (email) {
      window.location.href = `mailto:${email}`;
    }
  }

  viewProfile(candidateId: number) {
    this.router.navigate(['/candidate', candidateId]);
  }
}
