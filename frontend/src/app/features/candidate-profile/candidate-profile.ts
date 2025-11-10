// candidate-profile.component.ts
import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-candidate-profile',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './candidate-profile.html',
  styleUrls: ['./candidate-profile.css']
})
export class CandidateProfile{
  activeTab = 'description';

  candidate = {
    name: 'Ali Tufan',
    title: 'Senior UI/UX Designer',
    location: 'Cairo, Egypt',
    email: 'ali.tufan@example.com',
    phone: '+20 123 456 7890',
    image: 'https://i.pravatar.cc/300?img=3',
    salary: '$45k - $60k',
    experience: '5 Years',
    languages: ['English', 'Arabic', 'Spanish'],
  };

  tabs = [
    { id: 'description', label: 'Description' },
    { id: 'education', label: 'Education' },
    { id: 'experience', label: 'Experience' },
    { id: 'skills', label: 'Skills' },
  ];


  description = {
    text: `I’m top rated, highly client-oriented and self-organized UX/UI designer with 3+ years of comprehensive experience working across UX/UI, Web and Graphic Design. Have huge working experience in international teams in collaboration with art director and front-end devs. Have successful experience in startups as well as award-winning redesigns of existing projects.
      I take personal responsibility and pay attention to any detail of my work and can greatly help you with:
      Creating UX/UI for highloaded interface systems: CRM, SaaS, B2B, ERP systems, enterprise solutions and analytical systems with administration panels, dashboards, infographics and compex data
      Information architecting and creating UX/UI from scratch or low-fidelity wireframes to final visual UI design for web, mobile (iOS, Android) and desktop
      Product design for large international projects: monitoring and management web applications in health care, insurance, agroculture, banking and cryptocurrency area; SaaS web applications for marketing and analytics
      The better I do – the better I feel. My aim is to help you to convert the idea into successful product with the most effective, clean and aesthetic design. I’m passionately interested in challenging tasks and researching for intricate results.`,
  };

  education = [
    {
      university: 'Walters University',
      years: '2002 - 2004',
      degree: 'Masters In Fine Arts',
      description:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.',
    },
    {
      university: 'Tombers Collage',
      years: '2012 - 2015',
      degree: 'Bachelors In Fine Arts',
      description:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.',
    },
    {
      university: 'Imperial Institute of Art Direction',
      years: '2015 - 2017',
      degree: 'Diploma In Fine Arts',
      description:
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.',
    },
  ];

  experience = [
    {
      company: 'Creative Agency',
      years: '2018 - 2020',
      position: 'Senior Graphic Designer',
      description:
        'Worked on a wide range of branding and digital design projects. Collaborated closely with developers and clients to deliver creative solutions.',
    },
    {
      company: 'Art Studio',
      years: '2020 - 2022',
      position: 'Lead Visual Designer',
      description:
        'Led a team of designers and managed creative direction for various campaigns and visual identities.',
    },
    {
      company: 'DesignPro Inc.',
      years: '2022 - Present',
      position: 'Creative Director',
      description:
        'Overseeing art direction, UX/UI improvements, and visual branding strategies across multiple client projects.',
    },
  ];

  skills = [
    { name: 'Figma', level: 90 },
    { name: 'Adobe XD', level: 80 },
    { name: 'HTML/CSS', level: 85 },
    { name: 'Angular', level: 75 },
  ];

  // Contact form model (template-driven)
  contactModel = { name: '', email: '', message: '' };

  // methods
  setActive(tabId: string) {
    this.activeTab = tabId;
    // scroll to top of content if needed
    const el = document.querySelector('#tab-content');
    if (el) (el as HTMLElement).scrollTop = 0;
  }

  submitContact() {

    console.log('Contact submit:', this.contactModel);
    alert('Message sent (demo).');
    this.contactModel = { name: '', email: '', message: '' };
  }
}

