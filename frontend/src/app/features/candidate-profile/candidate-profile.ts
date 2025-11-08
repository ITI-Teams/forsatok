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
    { id: 'portfolio', label: 'Portfolio' },
    { id: 'skills', label: 'Skills' },
    { id: 'awards', label: 'Awards' },
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

  portfolio = [
    {
      name: 'Landing Page Design',
      desc: 'Creative landing page concept for a startup.',
      image: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f',
    },
    {
      name: 'Dashboard UI',
      desc: 'Modern dashboard design with dark mode support.',
      image: 'https://images.unsplash.com/photo-1509395176047-4a66953fd231',
    },
    {
      name: 'Mobile App Concept',
      desc: 'A UI concept for a finance tracking app.',
      image: 'https://images.unsplash.com/photo-1508931133309-29d92d362f7b',
    },
  ];

  skills = [
    { name: 'Figma', level: 90 },
    { name: 'Adobe XD', level: 80 },
    { name: 'HTML/CSS', level: 85 },
    { name: 'Angular', level: 75 },
  ];

  awards = [
    {
      title: 'Best Creative Designer',
      year: '2019',
      organization: 'Design Awards International',
      description:
        'Recognized for outstanding innovation and visual creativity in digital branding projects.',
    },
    {
      title: 'UI/UX Excellence Award',
      year: '2021',
      organization: 'Tech Design Expo',
      description:
        'Awarded for exceptional user experience design and impactful product interfaces.',
    },
    {
      title: 'Top Designer of the Year',
      year: '2023',
      organization: 'Creative Minds Conference',
      description:
        'Honored for leading-edge design strategy and contributions to creative community initiatives.',
    },
  ];

  reviews = [
    {
      name: 'John Doe',
      email: 'john@example.com',
      text: 'Great experience working with this designer!',
      rating: 5,
      image: 'https://randomuser.me/api/portraits/men/32.jpg',
    },
    {
      name: 'Sarah Smith',
      email: 'sarah@example.com',
      text: 'Very professional and creative approach.',
      rating: 4,
      image: 'https://randomuser.me/api/portraits/women/44.jpg',
    },
  ];


  newReview = {
    name: '',
    email: '',
    text: '',
    rating: 0,
    image: '',
  };

  submitReview() {
    if (!this.newReview.name || !this.newReview.email || !this.newReview.text || this.newReview.rating === 0 || !this.newReview.image) {
      alert('Please fill all fields and select a rating.');
      return;
    }

    this.reviews.push({ ...this.newReview });
    alert('✅ Thank you for your review!');
    this.newReview = { name: '', email: '', text: '', rating: 0, image: '' };
  }

  selectedImage: string | null = null;

  openImage(image: string) {
    this.selectedImage = image;
  }

  closeImage() {
    this.selectedImage = null;
  }


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

