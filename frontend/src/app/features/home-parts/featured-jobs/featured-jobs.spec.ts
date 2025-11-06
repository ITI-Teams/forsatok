import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FeaturedJobs } from './featured-jobs';

describe('FeaturedJobs', () => {
  let component: FeaturedJobs;
  let fixture: ComponentFixture<FeaturedJobs>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FeaturedJobs]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FeaturedJobs);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
